<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Hotel
 * @desc 酒店总控制器
 */
class Controller_Hotel extends Stourweb_Controller
{
    private $_typeid = 2;   //产品类型

    public function before()
    {
        parent::before();
        $channelname = Model_Nav::get_channel_name($this->_typeid);
        $this->assign('typeid', $this->_typeid);
        $this->assign('channelname', $channelname);
    }

    /**
     * 酒店首页
     */
    public function action_index()
    {
        $seoinfo = Model_Nav::get_channel_seo($this->_typeid);
        $this->assign('seoinfo', $seoinfo);
        $this->display('hotel/index');
    }

    /*
     * 酒店搜索页(搜索初始页)
     */
    public function action_search()
    {
        $this->display('hotel/search');
    }

    /*
     * 酒店搜索列表页
     * */
    public function action_list()
    {
        //参数处理
        $urlParam = $this->request->param('params');
        $destPy = 'all';
        $destId = $rankId = $priceId = $sortType = $keyword = $attrId = 0;
        $params = explode('-', str_replace('/', '-', $urlParam));
        $count = count($params);
        switch ($count)
        {
            //目的地
            case 1:
                list($destPy) = $params;
                break;
            //关键字、属性
            case 7:
                list($destPy, $rankId, $priceId, $sortType, $keyword, $attrId, $page) = $params;
                break;
        }
        $keyword = Arr::get($_GET, 'keyword');
        $page = $page < 1 ? 1 : $page;
        $destname = $destPy != 'all' ? ORM::factory('destinations')->where("pinyin='$destPy'")->find()->get('kindname') : '目的地';
        //获取seo信息
        $seo = Model_Hotel::search_seo($destPy);
        $this->assign('seoinfo', $seo);
        $this->assign('destpy', Common::remove_xss($destPy));
        $this->assign('destname', $destname);
        $this->assign('rankid', Common::remove_xss($rankId));
        $this->assign('sorttype', Common::remove_xss($sortType));
        $this->assign('keyword', Common::remove_xss($keyword));
        $this->assign('attrid', Common::remove_xss($attrId));
        $this->assign('priceid', Common::remove_xss($priceId));
        $this->assign('page', $page);
        $this->display('hotel/list');
    }

    /*
     * 酒店详细页
     */
    public function action_show()
    {
        $aid = Common::remove_xss($this->request->param('aid'));
        $info = ORM::factory('hotel')
            ->where("webid=" . $GLOBALS['sys_webid'] . " AND aid='$aid'")
            ->find()
            ->as_array();
        //扩展字段信息
        $extend_info = ORM::factory('hotel_extend_field')
            ->where("productid=" . $info['id'])
            ->find()
            ->as_array();
        //点击率加一
        Product::update_click_rate($aid, $this->_typeid);
        $seoinfo = Product::seo($info);
        $info['piclist'] = Product::pic_list($info['piclist']);
        $info['price'] = Model_Hotel::get_minprice($info['id']);
        $info['commentnum'] = Model_Comment::get_comment_num($info['id'], $this->_typeid); //评论次数
        $info['satisfyscore'] = Model_Comment::get_score($info['id'], $this->_typeid, $info['satisfyscore'], $info['commentnum']);//满意度
        $info['sellnum'] = Model_Member_Order::get_sell_num($info['id'], $this->_typeid) + intval($info['bookcount']); //销售数量
        $info['hotelrank'] = ORM::factory('hotel_rank', $info['hotelrankid'])->get('hotelrank');
        $this->assign('seoinfo', $seoinfo);
        $this->assign('info', $info);
        $this->assign('extendinfo', $extend_info);
        $this->display('hotel/show');
    }

    /*
     * 酒店预订页
     * */
    public function action_book()
    {
        $userinfo = Common::session('member');
        /* //检查是否是登陆界面.
         if(empty($userinfo))
         {
             $this->request->redirect('member/login');
         }*/
        $productid = Common::remove_xss($this->params['id']);
        $info = ORM::factory('hotel', $productid)->as_array();
        $info['price'] = Model_Hotel::get_minprice($info['id']);
        $this->assign('info', $info);
        $this->assign('userinfo', $userinfo);
        $this->display('hotel/book');
    }

    /*
     * 创建订单
     * */
    public function action_create()
    {
        $refer_url = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : $this->cmsurl;
        //套餐id
        $suitid = Arr::get($_POST, 'suitid');
        //联系人
        $linkman = Arr::get($_POST, 'linkman');
        //手机号
        $linktel = Arr::get($_POST, 'linktel');
        $linkidcard = Arr::get($_POST, 'linkidcard');
        //备注信息
        $remark = Arr::get($_POST, 'remark');
        //产品id
        $id = Arr::get($_POST, 'productid');
        //入住时间
        $startdate = Arr::get($_POST, 'startdate');
        //离店时间
        $leavedate = Arr::get($_POST, 'leavedate');
        //预订房间数
        $dingnum = Arr::get($_POST, 'dingnum');
        //验证部分
        $validataion = Validation::factory($_POST);
        $validataion->rule('linktel', 'not_empty');
        $validataion->rule('linktel', 'phone');
        $validataion->rule('linkman', 'not_empty');
        if (!$validataion->check())
        {
            $error = $validataion->errors();
            $keys = array_keys($validataion->errors());
            Common::message(array('message' => __("error_{$keys[0]}_{$error[$keys[0]][0]}"), 'jumpUrl' => $refer_url));
        }
        $info = ORM::factory('hotel')->where("id=$id")->find()->as_array();

        $suitArr = ORM::factory('hotel_room')
            ->where("id=:suitid")
            ->param(':suitid', $suitid)
            ->find()
            ->as_array();

        $priceArr = ORM::factory('hotel_room_price')->where("day=" . strtotime($startdate) . " and suitid=" . $suitid)->find()->as_array();
        if ($suitArr['paytype'] == '3')//这里补充一个当为二次确认时,修改订单为未处理状态.
        {
            $info['status'] = 0;
        }
        else
        {
            $info['status'] = 1;
        }
        $info['name'] = $info['title'] . "({$suitArr['roomname']})";
        $info['paytype'] = $suitarr['paytype'];
        $info['dingjin'] = intval($suitArr['dingjin']);
        $info['jifentprice'] = intval($suitArr['jifentprice']);
        $info['jifenbook'] = intval($suitArr['jifenbook']);
        $info['jifencomment'] = intval($suitArr['jifencomment']);
        $info['ourprice'] = intval($priceArr['price']);
        $info['childprice'] = 0;
        $info['usedate'] = $startdate;
        $info['departdate'] = $leavedate;
        $userinfo = Common::session('member');
        if (!isset($userinfo['mid']))//如果未登陆
        {
            $sql = "SELECT * FROM `sline_member` WHERE mobile='$linktel' LIMIT 1";
            $row = DB::query(1, $sql)->execute()->as_array();
            if (!empty($row[0]['mid']))
            {
                $mid = $row[0]['mid'];
            }
            else //如果不存在则创建新用户
            {
                $model = ORM::factory('member');
                $model->mobile = $linktel;
                $model->pwd = md5($linktel);
                $model->nickname = substr_replace($linktel, '***', 3, 3);
                $model->save();
                if ($model->saved()) //注册成功
                {
                    $mid = $model->mid;
                }
                else
                {
                    $mid = 0;
                }
            }
        }
        else
        {
            $mid = $userinfo['mid'];
        }
        $ordersn = Product::get_ordersn('02');
        $arr = array(
            'ordersn' => $ordersn,
            'webid' => 0,
            'typeid' => $this->_typeid,
            'productautoid' => $id,
            'productaid' => $info['aid'],
            'productname' => $info['name'],
            'litpic' => $info['litpic'],
            'price' => $info['ourprice'],
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
            'suitid' => $suitid,
            'remark' => $remark,
            'status' => $info['status'] ? $info['status'] : 0
        );
        //添加订单
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
                $url = Common::get_web_url($info['webid']).'/hotels/show_'.$info['aid'].'.html';
                $this->request->redirect($url);
            }

        }
    }

    /**
     * ajax请求 加载更多
     */
    public function action_ajax_hotel_more()
    {
        $urlParam = $this->request->param('params');
        $keyword = Arr::get($_GET, 'keyword') ? Arr::get($_GET, 'keyword') : '';
        $data = Model_Hotel::search_result($urlParam, $keyword);
        echo ($data);
    }

    /*
     * 获取房型进店和离店日期价格
     * */
    public function action_ajax_range_price()
    {
        $startdate = Arr::get($_GET, 'startdate');
        $enddate = Arr::get($_GET, 'leavedate');
        $suitid = Arr::get($_GET, 'suitid');
        $dingnum = Arr::get($_GET, 'dingnum');
        $price = Model_Hotel::suit_range_price($suitid, $startdate, $enddate, $dingnum);
        echo json_encode(array('price' => $price));
    }

    /**
     *
     * 获取套餐可预订的最小日期.
     *
     */
    public function action_ajax_mindate_book()
    {
        $suitid = Arr::get($_GET, 'suitid');
        $day = Model_Hotel::suit_mindate_book($suitid);
        echo json_encode(array(
            'startdate' => date('Y-m-d', $day),
            'enddate' => date('Y-m-d', strtotime("+1 day", $day))
        ));
    }

    /**
     *
     * 检测库存是否能够预订
     */
    public function action_ajax_check_storage()
    {
        $startdate = Arr::get($_POST, 'startdate');
        $enddate = Arr::get($_POST, 'enddate');
        $dingnum = Arr::get($_POST, 'dingnum');
        $suitid = Arr::get($_POST, 'suitid');
        $flag = Model_Hotel::check_suit_storage($suitid, $startdate, $enddate, $dingnum);
        echo json_encode(array('status' => $flag));
    }
}