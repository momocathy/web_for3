<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Visa 签证
 */
class Controller_Visa extends Stourweb_Controller
{
    private $_typeid = 8;   //产品类型

    public function before()
    {
        parent::before();
        $channelname = Model_Nav::get_channel_name($this->_typeid);
        $this->assign('typeid', $this->_typeid);
        $this->assign('channelname', $channelname);
    }

    /**
     * 线路首页
     */
    public function action_index()
    {
        $seoinfo = Model_Nav::get_channel_seo($this->_typeid);
        $this->assign('seoinfo', $seoinfo);
        $this->display('visa/index');
    }

    /**
     * 线路列表URL
     */
    public function action_list()
    {
        $uri = $this->request->param('params');
        $data = $this->_explode_url($uri);
        $area = Model_Visa::vias_area($data['area']);
        $area['litpic'] = Common::img($area['litpic']);
        $city = Model_Visa::visa_city();
        $area['title'] = $area['kindname'] . '_' . $this->channelname;
        $seoInfo = Model_Visa::search_seo($data['area']);
        $this->assign('seoinfo', $seoInfo);
        $this->assign('area', $area);
        $this->assign('city', $city);
        $this->assign('areapy', $data['area']);
        $this->assign('page', $data['page']);
        $this->assign('cityid', $data['cityid']);
        $this->assign('typeid', $data['typeid']);
        $this->display('visa/list');
    }

    /**
     * 签证搜索
     */
    public function action_search()
    {
        $seoinfo = Model_Nav::get_channel_seo($this->_typeid);
        $this->assign('seoinfo', $seoinfo);
        $this->display('visa/search');
    }

    /**
     * 分隔URL参数 visa/area-cityid-typeid-page
     * @return mixed
     */
    private function _explode_url($uri)
    {
        $params = explode('-', str_replace('/', '-', Common::remove_xss($uri)));
        $count = count($params);
        if ($count == 0)
        {
            exit;
        }
        switch ($count)
        {
            case 1:
                list($data['area']) = $params;
                $data['cityid'] = $data['typeid'] = 0;
                break;
            case 2:
                list($data['area'], $data['cityid']) = $params;
                $data['typeid'] = 0;
                break;
            case 3:
                list($data['area'], $data['cityid'], $data['typeid']) = $params;
                break;
            case 4:
                list($data['area'], $data['cityid'],$data['typeid'], $data['page']) = $params;
                break;
        }
        //分页
        $data['page'] = empty($data['page']) ? 1 : $data['page'];
        return $data;
    }

    /**
     * AJAX 获取签证列表数据
     */
    public function action_ajax_visa_more()
    {
        $params = $this->request->param('params');
        $uri = $this->_explode_url($params);
        $data=$this->visa_data($uri);
        echo Product::list_search_format($data, $uri['page']);
    }

    public function action_ajax_area()
    {
        $params = Common::remove_xss($_POST['area']);
        $data = Model_Visa::vias_area($params, 'kindname');
        echo empty($data) ? '0' : Common::get_web_url($GLOBALS['$sys_webid']) . "/visa/{$data['pinyin']}";
    }

    /**
     * 签证列表数据
     * @param $uri
     * @return mixed
     */
    private function visa_data($uri)
    {
        $data = Model_Visa::parse_url($uri);
        foreach ($data as &$v)
        {
            $v['url'] = Common::get_web_url($v['webid']) . "/visa/show_{$v['aid']}.html";
            $v['litpic'] = Common::img($v['litpic']);
            $city = Model_Visa::visa_city_by_id($v['cityid']);
            if (is_array($city))
            {
                $city = $city['kindname'];
            }
            $v['city'] = $city;
        }
        return $data;
    }

    /**
     * 签证详情
     */
    public function action_show()
    {
        $aid = Common::remove_xss($this->request->param('aid'));
        $info = Model_Visa::visa_detail($aid);
        //点击率加一
        Product::update_click_rate($aid, $this->_typeid);
        $info['commentnum'] = Model_Comment::get_comment_num($info['id'], $this->_typeid);
        $info['satisfyscore'] = Model_Comment::get_score($info['id'], $this->_typeid, $info['satisfyscore'], $info['commentnum']);//满意度
        $info['sellnum'] = Model_Member_Order::get_sell_num($info['id'], $this->_typeid) + intval($info['bookcount']);
        $extend_info = Model_Visa::visa_extend($info['id']);
        $seoInfo = Product::seo($info);
        $this->assign('info', $info);
        $this->assign('seoinfo', $seoInfo);
        $this->assign('extendinfo', $extend_info);
        $this->display('visa/show');
    }

    /**
     * 签证预订
     */
    public function action_book()
    {
        $userinfo = Common::session('member');
        $productid = Common::remove_xss($this->params['id']);
        $info = ORM::factory('visa', $productid)->as_array();
        $info['litpic'] = Common::img($info['litpic'], 150, 90);
        $this->assign('info', $info);
        $this->assign('userinfo', $userinfo);
        $this->display('visa/book');
    }

    /**
     * 团购订单
     */
    public function action_create()
    {
        $refer_url = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : $this->cmsurl;
        //联系人
        $linkman = Arr::get($_POST, 'linkman');
        //手机号
        $linktel = Arr::get($_POST, 'linktel');
        //身份证
        $linkidcard = Arr::get($_POST, 'linkidcard');
        //备注信息
        $remark = Arr::get($_POST, 'remark');
        //产品id
        $id = Arr::get($_POST, 'productid');
        //预订数量
        $dingnum = Arr::get($_POST, 'dingnum');
        //验证部分
        $validataion = Validation::factory($_POST);
        $validataion->rule('linktel', 'not_empty');
        $validataion->rule('linktel', 'phone');
        $validataion->rule('linkman', 'not_empty');
        $validataion->rule('dingnum', 'regex', array(':value', '/^[1-9]+$/'));
        if (!$validataion->check())
        {
            $error = $validataion->errors();
            $keys = array_keys($validataion->errors());
            Common::message(array('message' => __("error_{$keys[0]}_{$error[$keys[0]][0]}"), 'jumpUrl' => $refer_url));
        }
        //二次验证
        $info = Model_Visa::visa_detail_id(intval($id));
        //检测用户是否存在
        $userinfo = Common::session('member');
        $mid = $userinfo['mid'];
        if (!isset($userinfo['mid']))
        {
            $user = Model_Member::member_find($linktel);
            if (empty($user))
            {
                $user = Model_Member::register(array('mobile' => $linktel, 'pwd' => md5($linktel)));
                $mid = $user[0];
            }
            else
            {
                $mid = $user['mid'];
            }
        }
        if($info['paytype']=='3')//这里补充一个当为二次确认时,修改订单为未处理状态.
        {

            $info['status'] = 0;

        }
        else
        {

            $info['status'] = 1;

        }
        //合并生成订单
        $ordersn = Product::get_ordersn('08');
        $arr = array(
            'ordersn' => $ordersn,
            'webid' => 0,
            'typeid' => $this->_typeid,
            'productautoid' => $id,
            'productaid' => $info['aid'],
            'productname' => $info['title'],
            'litpic' => $info['litpic'],
            'price' => $info['price'],
            'childprice' => $info['childprice'],
            'jifentprice' => $info['jifentprice'],
            'jifenbook' => $info['jifenbook'],
            'jifencomment' => $info['jifencomment'],
            'paytype' => $info['paytype'],
            'dingjin' => $info['dingjin'],
            'usedate' => $info['usedate'],
            'departdate' => $info['departdate'],
            'addtime' => time(),
            'memberid' => $mid,
            'dingnum' => $dingnum,
            'childnum' => 0,
            'oldprice' => 0,
            'oldnum' => 0,
            'linkman' => $linkman,
            'linktel' => $linktel,
            'linkidcard' => $linkidcard,
            'suitid' => 0,
            'remark' => $remark,
            'status' => $info['status'] ? $info['status'] : 0
        );
        //添加订单,跳转支付
        if (Product::add_order($arr))
        {
            $sql = "SELECT id FROM `sline_member_order` WHERE ordersn='$ordersn'";
            $ar = DB::query(1, $sql)->execute()->as_array();
            if($info['status'] == 1)
            {
                $this->request->redirect('pub/pay/orderid/' . $ar[0]['id']);
            }
            else
            {
                $url = Common::get_web_url($info['webid']).'/visa/show_'.$info['aid'].'.html';
                $this->request->redirect($url);
            }
        }
    }
}