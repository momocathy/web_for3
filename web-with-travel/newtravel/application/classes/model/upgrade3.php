<?php defined('SYSPATH') or die('No direct access allowed.');

/*
 * 系统升级类(新版本)
 * */

class Model_Upgrade3
{

    private $api_url = 'http://update.souxw.com/service/api_v3.ashx?';
    private $serialnumber = null;
    private $appidentity = null;

    private $token = null; //令牌

    private $currentversion = null;
    private $upgrade_product_code = null;


    public function __construct()
    {
        include(Kohana::find_file('data', 'license'));
        $this->serialnumber = $SerialNumber;
        $this->appidentity = $_SERVER['HTTP_HOST'];
    }

    public function initialise($systempart)
    {
        $this->upgrade_product_code = $systempart['pcode'];
        $this->currentversion = $systempart['cVersion'];
    }


    /*
     * 获取token
     * */
    public function getToken()
    {
        $token = trim($this->readToken());
        if (!empty($token))
        {
            $arr = array(
                'action' => 'getupgradestatus',
                'token' => $token
            );
            $params = http_build_query($arr); //生成参数数组
            $url = $this->api_url . $params;
            $data = $this->http($url);
            if ($data['Success'] == 1)
                $this->token = $token;
            else
                $token = '';
        }

        if (empty($token))
        {
            $arr = array(
                'action' => 'gettoken',
                'productcode' => $this->upgrade_product_code,
                'appidentity' => $this->appidentity,
                'serialnumber' => $this->serialnumber
            );
            $params = http_build_query($arr); //生成参数数组
            $url = $this->api_url . $params;
            $data = $this->http($url);
            if ($data['Success'] == 1)
                $this->token = trim($data['Data']);
            else
                $this->token = "";

            $this->writeToken($this->token);
        }
    }

    /*
     * 读token
     * */
    private function readToken()
    {
        $currentsession = Session::instance();
        $token = $currentsession->get($this->upgrade_product_code . '_token');

        $this->writeTokenFile($token);
        return $token;
    }

    /*
 * 写token
 * */
    private function writeToken($token)
    {
        $currentsession = Session::instance();
        $currentsession->set($this->upgrade_product_code . '_token', $token);

        $this->writeTokenFile($token);
    }

    private function writeTokenFile($token)
    {
        if(empty($token))
            return;

        $file = BASEPATH . "/data/token/{$token}.php";
        if (is_file($file))
            return;
        if (!is_dir(dirname($file)))
            mkdir(dirname($file), 0777, true);

        $fp = fopen($file, 'wb');
        flock($fp, 3);
        fwrite($fp, $token);
        fclose($fp);

        $dh = opendir(dirname($file));
        if (!$dh)
            return;

        while ($object = readdir($dh))
        {
            if ($object != "." && $object != "..")
            {
                $fullname = dirname($file) . "/" . $object;
                if (is_file($fullname) && (time() - filemtime($fullname) > 60 * 60 * 24))
                    unlink($fullname);
            }
        }

        closedir($dh);
    }

    /*
     * 检测新版本补丁
     * */
    public function checkNewestPatch()
    {
        $arr = array(
            'action' => 'checknewestpatch',
            'productcode' => $this->upgrade_product_code,
            'currentversion' => $this->currentversion
        );
        $params = http_build_query($arr); //生成参数数组
        $url = $this->api_url . $params;
        return $this->http($url);
    }

    //版本信息
    public function releaseFeedback()
    {
        $arr = array(
            'action' => 'releasefeedback',
            'productName' => $this->upgrade_product_code,
            'version' => $this->currentversion,
            'domainname' => $_SERVER['HTTP_HOST'],
            'serverip' => $_SERVER["SERVER_ADDR"],
            'sitename' => $_SERVER["SERVER_NAME"],
            'serialnumber' => $this->serialnumber
        );
        $params = http_build_query($arr); //生成参数数组
        $url = $this->api_url . $params;
        return $this->http($url);
    }

    /*
     * 获得最新补丁下载地址
     * */
    public function getNewVersion()
    {
        $this->getToken();
        $arr = array(
            'action' => 'getnewestpatch',
            'token' => $this->token,
            'currentversion' => $this->currentversion
        );
        $params = http_build_query($arr); //生成参数数组
        $url = $this->api_url . $params;
        return $this->http($url);

    }

    /*
     * 获取已升级的补丁
     * */
    public function getOldPatch($count)
    {
        $this->getToken();
        $arr = array(
            'action' => 'getoldpatch',
            'token' => $this->token,
            'currentversion' => $this->currentversion,
            'count' => $count
        );
        $params = http_build_query($arr); //生成参数数组
        $url = $this->api_url . $params;
        return $this->http($url);
    }

    /*
     * 获取最新的升级补丁信息
     * */
    public function getLastPatch()
    {
        $this->getToken();
        $arr = array(
            'action' => 'getlastpatch',
            'token' => $this->token,
            'currentversion' => $this->currentversion

        );
        $params = http_build_query($arr); //生成参数数组
        $url = $this->api_url . $params;
        return $this->http($url);
    }

    /*
     * 返回登记升级信息
     * */
    public function regUpgradeStatus($patchurl)
    {
        $arr = array(
            'action' => 'releaseupgraderegist',
            'patchsn' => $patchurl

        );
        $params = http_build_query($arr); //生成参数数组
        $url = $this->api_url . $params;
        return $this->http($url);
    }

    /*
     * 检测当前站是否为内测站
     * */
    public function isInternalTestAppIdentity()
    {
        $arr = array(
            'action' => 'IsInternalTestAppIdentity',
            'appidentity' => $this->appidentity
        );
        $params = http_build_query($arr); //生成参数数组
        $url = $this->api_url . $params;
        return $this->http($url);
    }

    /*
    * 接口请求函数
    * @param string url
    * @param string postfields,post请求附加字段.
    * @return $response
    * */
    private function http($url, $postfields = '', $method = 'GET')
    {
        $ci = curl_init();

        curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);

        if ($method == 'POST')
        {
            curl_setopt($ci, CURLOPT_POST, TRUE);
            if ($postfields != '') curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
        }

        curl_setopt($ci, CURLOPT_URL, $url);
        $response = json_decode(curl_exec($ci), true);
        curl_close($ci);
        return $response;
    }

    /*
     * 检测版是否是正版
     * */
    public function checkRightV()
    {
        $newversion = $this->getNewVersion();
        return $newversion['Success'] == true ? 1 : 0;
    }

    //返回序列号
    public function getSerialnumber()
    {
        return $this->serialnumber;
    }


    //重写版本号
    public static function rewriteVersion($file, $pcode, $ver, $beta, $pubdate)
    {
        @chmod($file, 0777);
        if (!is_writeable($file))
        {
            return false;
        }
        $versiontype = ($beta == 1) ? '测试版' : '正式版';
        $pubdate = Common::myDate('Y-m-d', strtotime($pubdate));
        $fp = fopen($file, 'w');
        flock($fp, 3);
        fwrite($fp, "<" . "?php\r\n");
        fwrite($fp, "\$pcode = '{$pcode}';\r\n");
        fwrite($fp, "\$cVersion ='" . $ver . "';\r\n");
        fwrite($fp, "\$versiontype ='" . $versiontype . "';\r\n");
        fwrite($fp, "\$pubdate ='" . $pubdate . "';\r\n");
        fwrite($fp, "?" . ">");
        fclose($fp);

        return true;
    }

    //重写序列号
    public static function rewriteLicense($licenseid)
    {
        $file = Kohana::find_file('data', 'license');
        @chmod($file, 0777);
        if (!is_writeable($file))
        {
            return false;
        }

        $fp = fopen($file, 'w');
        flock($fp, 3);
        fwrite($fp, "<" . "?php\r\n");
        fwrite($fp, "\$SerialNumber ='" . $licenseid . "';\r\n");
        fwrite($fp, "?" . ">");
        fclose($fp);

        return true;
    }


    /*
     * 生成大小
     * */
    public static function format_bytes($size)
    {
        $units = array(' B', ' KB', ' MB', ' GB', ' TB');
        for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
        return round($size, 2) . $units[$i];
    }

    /*
     * 特殊处理,处理描述
     * */
    public static function genDesc($description)
    {
        $out = '<ul>';
        $ar = explode('<br>', $description);
        foreach ($ar as $v)
        {
            $out .= '<li>' . $v . '</li>';
        }
        $out .= '</ul>';
        return $out;
    }

}