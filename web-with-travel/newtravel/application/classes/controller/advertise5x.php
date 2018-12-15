<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Advertise5x extends Stourweb_Controller
{
    /*
     * 广告总控制器
     * */
    public static $rollAd = array(
        'IndexSpotRollingAd',
        'HotelRollingAd',
        'PerformRollingAd',
        'ProductRollingAd',
        'SelftripRollingAd',
        'SpotRollingAd',
        'SpotSuitAd',
        'IndexRollingAd',
        'LineRollingAd',
        'NewsRollingAd'
    );

    public function before()
    {
        parent::before();
        //是否是手机版
        $ismobile = $this->params['ismobile'] ? $this->params['ismobile'] : 0;
        $this->assign('parentkey', $this->params['parentkey']);
        $this->assign('itemid', $this->params['itemid']);
        $this->assign('ismobile', $ismobile);
        $this->assign('weblist', Common::getWebList());
    }

    /**
     * 广告列表
     */
    public function action_index()
    {
        $action = is_null($this->params['action']) ? 'null' : $this->params['action'];
        switch ($action)
        {
            case 'null':
                $this->assign('position', $this->ads_position());
                $this->display('stourtravel/advertise5x/list');
                break;
            case 'read':
                $start = Arr::get($_GET, 'start');
                $limit = Arr::get($_GET, 'limit');
                $keyword = Arr::get($_GET, 'keyword');
                $prefix = Arr::get($_GET, 'adtype');
                $webid = Arr::get($_GET, 'webid');
                $sort = json_decode(Arr::get($_GET, 'sort'), true);
                $ismobile = $this->params['ismobile'];
                $where = !empty($webid) ? "a.webid={$webid}" : 'a.webid=0';
                if (!empty($keyword))
                {
                    $where .= " and (p.kindname like '%{$keyword}%' or a.prefix like '%{$keyword}%')";
                }
                $order = 'order by p.id asc,a.is_system asc,a.number asc';
                $where .= !empty($prefix) ? " and a.prefix='{$prefix}'" : '';
                $where .= !empty($ismobile) ? " and a.is_pc='0'" : " and a.is_pc='1'";
                $sql = "select a.*,p.kindname from sline_advertise_5x as a left join sline_page as p on a.prefix=p.pagename having {$where} {$order} limit {$start},{$limit}";
                $totalcount_arr = DB::query(Database::SELECT, "select a.*,p.kindname from sline_advertise_5x as a left join sline_page as p on a.prefix=p.pagename having {$where}")->execute()->as_array();
                $list = DB::query(Database::SELECT, $sql)->execute()->as_array();
                foreach ($list as $k => &$v)
                {
                    $v['prefix'] = (int)$v['is_system'] == 1 ? 's_' . $v['prefix'] : 'c_' . $v['prefix'];
                }
                $result['total'] = $totalcount_arr[0]['num'];
                $result['lists'] = $list;
                $result['success'] = true;
                echo json_encode($result);
                break;
            case 'delete':
                $rawdata = file_get_contents('php://input');
                $data = json_decode($rawdata);
                $id = $data->id;
                if (is_numeric($id))
                {
                    $sql = "delete from sline_advertise_5x where id={$id} and is_system='0'";
                    $total_rows = DB::query(Database::DELETE, $sql)->execute();
                    echo $total_rows > 0 ? 'ok' : 'no';
                }
                break;
        }
    }

    /**
     * 添加广告
     */
    public function action_add()
    {
        //手机版广告
        $title = isset($this->params['mobile']) ? '添加手机广告' : '添加广告';
        $this->assign('action', 'add');
        $this->assign('title', $title);
        $this->assign('issystem', $this->params['issystem']);
        $this->assign('ismobile', $this->params['ismobile']);
        $this->assign('position', $this->ads_position());
        $this->display('stourtravel/advertise5x/edit');
    }

    /**
     * 修改广告
     */
    public function action_edit()
    {
        $id = $this->params['id'];
        $info = DB::query(Database::SELECT, "select a.*,p.kindname from sline_advertise_5x as a left join sline_page as p on a.prefix=p.pagename having a.id={$id}")->execute()->as_array();
        $adsrc = unserialize($info[0]['adsrc']);
        $adname = unserialize($info[0]['adname']);
        $adlink = unserialize($info[0]['adlink']);
        $image = array();
        foreach ($adsrc as $k => $r)
        {
            $image[] = array($adsrc[$k], $adname[$k], $adlink[$k]);
        }
        $info = $info[0];
        $info['image'] = $image;
        $info['is_system']=$info['is_system']>0?'s_':'c_';
        $info['position'] =$info['is_system'].$info['prefix'] . '_' . $info['number'];
        $info['kindlist_arr'] = Model_Destinations::getKindlistArr($info['kindlist']);
        $this->assign('info', $info);
        $this->assign('action', 'edit');
        $this->assign('title', '修改广告');
        $this->assign('position', $this->ads_position());
        $this->display('stourtravel/advertise5x/edit');
    }

    /**
     * 获取广告定位
     * @return array
     */
    private function ads_position()
    {
        $data = DB::query(Database::SELECT, 'select * from sline_page where pid=0')->execute()->as_array();
        foreach ($data as $k => $v)
        {
            $data[$k]['sub'] = DB::query(Database::SELECT, "select * from sline_page where pid={$v['id']}")->execute()->as_array();
        }
        return $data;
    }

    /**
     * 获取广告位标示
     */
    public function action_ajax_number()
    {
        $prefix = Arr::get($_POST, 'prefix');
        $issystem = Arr::get($_POST, 'is_system');
        $ispc = Arr::get($_POST, 'is_pc');
        $num = DB::query(Database::SELECT, "select number from sline_advertise_5x where prefix='{$prefix}' and is_system='{$issystem}' and is_pc='{$ispc}' order by number desc limit 1")->execute()->as_array();
        $num = empty($num) ? 1 : ($num[0]['number'] + 1);
        echo $num;
    }

    /**
     * ajax保存广告
     */
    public function action_ajax_save()
    {
        $status = false;
        $_POST['kindlist'] = implode(',', $_POST['kindlist']);
        $_POST['adsrc'] = serialize($_POST['adsrc']);
        $_POST['adlink'] = serialize($_POST['adlink']);
        $_POST['adname'] = serialize($_POST['adname']);
        $action = $_POST['action'];
        $id = $_POST['id'];
        unset($_POST['action']);
        unset($_POST['id']);//var_dump($_POST,array_keys($_POST),array_values($_POST));
        if ($action == 'add' && empty($id))
        {
            $result = DB::insert('advertise_5x', array_keys($_POST))->values(array_values($_POST))->execute();
            if (is_array($result))
            {
                $id = $result[0];
                $status = true;
            }
        }
        else
        {
            $sql = array();
			unset($_POST['is_system']);
            foreach ($_POST as $k => $v)
            {
                array_push($sql, $k . "='{$v}'");
            }
            $sql=implode(',', $sql);
            $sql = "UPDATE `sline_advertise_5x` SET {$sql} WHERE `id` = {$id}";
            $result = DB::query(3, $sql)->execute();
            if ($result)
            {
                $status = true;
            }
        }
        echo json_encode(array('status' => $status, 'productid' => $id));
    }

    /**
     * ajax 切换广告位显示状态
     */
    public function action_ajax_statu()
    {
        $statu = (int)$_GET['statu'];
        $id = $_GET['id'];
        if ($statu > 1 || $statu < 0)
        {
            exit('0');
        }
        $rows = DB::update('advertise_5x')->set(array('is_show' => "$statu"))->where("id={$id}")->execute();
        echo $rows > 0 ? true : false;
    }
}