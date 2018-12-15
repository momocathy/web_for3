<?php

/**
 * 网页地址与手机地址转换类
 *
 * @version        $Id: mobile.class.php
 * @package        Stourweb.Libraries
 * @copyright      Copyright (c) 2007 - 2014, Inc.
 * @license        http://www.stourweb.com
 */
class Mobile
{
    var $userName = ''; //用户名
    var $password = ''; //密码
    var $sendphone;//发送的手机号
    var $contentprefix;//称昵
    var $content;
    var $apiUrl = 'http://sms.souxw.com/service/api.ashx?'; //短信接口地址
    var $action;//操作 buysms/sendsms/querysmssendlog/querysmsbuylog/querysmsbalance
    var $tables = array(
        "lines" => array('#@__line', 'lines'),
        'hotels' => array('#@__hotel', 'hotels'),
        'cars' => array('#@__car', 'cars'),
        'raiders' => array('#@__article', 'raider'),
        'spots' => array('#@__spot', 'spot'),
        'photos' => array('#@__photo', 'photo'),
        'tuan' => array('#@__tuan', 'tuan'),
        'visa' => array('#@__visa', 'visa')
    );

    function __construct()
    {
    }

    /*
     * 分析手机访问的地址
     * @param string url
     * @return string mobileurl
     * */
    public function getMobileUrl($url)
    {
        global $cfg_basehost, $cfg_mobile_version;
        if ($cfg_mobile_version > 0)
        {
            $config = require_once(dirname(__DIR__) . '/data/mobile.php');
            $host=parse_url($url, PHP_URL_HOST);
            $mobileHost=parse_url($config['domain']['mobile'],PHP_URL_HOST);
            $mainHost=parse_url($config['domain']['main'],PHP_URL_HOST);
            if (empty($config['domain']['mobile']) || $config['domain']['mobile'] == $config['domain']['main'] || $host!=$mainHost)
            {
                $path = $config['version'][$cfg_mobile_version]['path'];
                if (!preg_match("`^" . str_replace('.', '\.', $config['domain']['main'] . $path) . "`", $url))
                {
                    $url=str_replace($host,$host.$path,$url);
                }
            }
            else
            {
                $url = str_replace($host,$mobileHost,$url);
            }
            $url = preg_replace('`([^:])//`', '$1/',$url);
            return $url;
        }
        $channel = array('lines', 'hotels', 'cars', 'raiders', 'spots', 'photos', 'visa', 'tuan');
        $arr = explode('/', $url);
        $pathinfo = array_slice($arr, -2, 2);//取最后两个数据
        if (in_array($pathinfo[0], $channel)) //这里只判断栏目首页和显示页
        {
            if (empty($pathinfo[1])) //栏目首页
            {
                $item = $this->tables[$pathinfo[0]][1];
                $mobileurl = $GLOBALS['cfg_basehost'] . '/shouji/' . $item . '/';
            }
            else if (Helper_Archive::strHasStr($pathinfo[1], 'show'))//详细显示页面
            {
                $ar = explode('_', str_replace('.html', '', $pathinfo[1]));
                $aid = $ar[1];//文章aid
                $mobileurl = self::getMobileShowUrl($pathinfo[0], $aid);
            }
            else
            {
                $mobileurl = $cfg_basehost . '/shouji/';//首页
            }
        }
        else
        {
            $mobileurl = $cfg_basehost . '/shouji/';//首页
        }
        return $mobileurl;
    }

    /*
     * 获取详细页面mobile显示url
     * */
    public function getMobileShowUrl($type, $aid)
    {
        $info = Helper_Archive::getSlineWebInfo();
        if (!empty($info['webid']))
        {
            $table = $this->tables[$type][0];
            $where = "aid='$aid' and webid='{$info['webid']}'";
            Helper_Archive::loadModule('common');
            $model = new CommonModule($table);
            $id = $model->getField('id', $where);
            $url = $GLOBALS['cfg_basehost'] . '/shouji/' . $this->tables[$type][1] . '/show/id/' . $id;
        }
        else
        {
            //子战跳转
            $table = $this->tables[$type][0];
            $where = "aid='$aid' and webid='{$info['id']}'";
            Helper_Archive::loadModule('common');
            $model = new CommonModule($table);
            $id = $model->getField('id', $where);
            $url = $GLOBALS['cfg_basehost'] . '/shouji/' . $this->tables[$type][1] . '/show/id/' . $id;
        }
        return $url;
    }
}