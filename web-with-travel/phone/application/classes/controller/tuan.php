<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Tuan 团购
 */
class Controller_Tuan extends Stourweb_Controller
{
    private $_typeid = 13;

    public function before()
    {
        parent::before();
        $this->assign('typeid', $this->_typeid);
        $this->assign('channelname', Model_Nav::get_channel_name($this->_typeid));
    }

    /**
     * 团购首页
     */
    public function action_index()
    {
        $seoinfo = Model_Nav::get_channel_seo($this->_typeid);
        $this->assign('seoinfo', $seoinfo);
        $this->display('tuan/index');
    }

    /**
     * 团购列表
     */
    public function action_list()
    {
        $uri = $this->request->param('params');
        $data = $this->_explode_url($uri);
        $destname = $data['destPy'] != 'all' ? ORM::factory('destinations')->where("pinyin='{$data['destPy']}'")->find()->get('kindname') : '目的地';
        //获取seo信息
        $seo = Model_Tuan::search_seo($data['destPy']);
        $this->assign('seoinfo', $seo);
        $this->assign('destpy', $data['destPy']);
        $this->assign('status', $data['status']);
        $this->assign('destname', $destname);
        $this->assign('attrid', $data['attrId']);
        $this->assign('page', $data['page']);
        $this->assign('sorttype', $data['sorttype']);
        $this->assign('keyword', $data['keyword']);
        $this->display('tuan/list');
    }

    /**
     * 团购搜索
     */
    public function action_search()
    {
        $this->display('tuan/search');
    }

    /**
     * 分隔URL参数 tuan/destPy-status-attrId-page?sorttype&keyword
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
                $data['status'] = $data['attrId'] = 0;
                list($data['destPy']) = $params;
                break;
            case 2:
                $data['attrId'] = 0;
                list($data['destPy'], $data['status']) = $params;
                break;
            case 3:
                list($data['destPy'], $data['status'], $data['attrId']) = $params;
                break;
            case 4:
                list($data['destPy'], $data['status'], $data['attrId'], $data['page']) = $params;
                break;
        }
        //分页
        $data['page'] = empty($data['page']) ? 1 : $data['page'];
        //关键字
        $data['keyword'] = empty($_GET['keyword']) ? '' : $_GET['keyword'];
        //排序
        $data['sorttype'] = empty($_GET['sorttype']) ? 0 : $_GET['sorttype'];
        return $data;
    }

    /**
     * AJAX 获取团购列表数据
     */
    public function action_ajax_tuan_data()
    {
        $uri = $this->request->param('params');
        $uri = $this->_explode_url($uri);
        $data = Model_Tuan::list_data($uri);
        foreach ($data as &$v)
        {
            $v['url'] = Common::get_web_url($v['webid']) . "/tuan/show_{$v['aid']}.html";
            $v['litpic'] = Common::img($v['litpic']);
            $v['sellnum'] = Model_Member_Order::get_sell_num($v['id'], $this->_typeid) + intval($v['bookcount']);
            $v['discount'] = $v['price'] == $v['sellprice'] ? '不打' : round($v['price'] / $v['sellprice'], 2) * 10;
        }
        echo Product::list_search_format($data, $uri['page']);
    }

    /**
     * 团购详情
     */
    public function action_show()
    {
        $aid = Common::remove_xss($this->request->param('aid'));
        $row = Model_Tuan::tuan_detail($aid);
        //点击率加一
        Product::update_click_rate($aid, $this->_typeid);
        //评论次数
        $row['commentnum'] = Model_Comment::get_comment_num($row['id'], $this->_typeid);
        $row['satisfyscore'] = Model_Comment::get_score($row['id'],  $this->_typeid, $row['satisfyscore'], $row['commentnum']);//满意度
        //销售数量
        $row['sellnum'] = Model_Member_Order::get_sell_num($row['id'], $this->_typeid) + intval($row['bookcount']);
        //产品图片
        $row['piclist'] = Product::pic_list($row['piclist']);
        //折扣
        $row['discount'] = round($row['price'] / $row['sellprice'], 2) * 10;
        $extend_info = Model_Tuan::tuan_extend($row['id']);
        $seoInfo = Product::seo($row);
        $this->assign('info', $row);
        $this->assign('seoinfo', $seoInfo);
        $this->assign('extendinfo', $extend_info);
        $this->display('tuan/show');
    }

    /**
     * 团购预订
     */
    public function action_book()
    {
        $userinfo = Common::session('member');
        $productid = Common::remove_xss($this->params['id']);
        $info = ORM::factory('tuan', $productid)->as_array();
        $info['litpic'] = Common::img($info['litpic'], 150, 90);
        $this->assign('info', $info);
        $this->assign('userinfo', $userinfo);
        $this->display('tuan/book');
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
        $info = Model_Tuan::tuan_detail_id(intval($id));
        if($info['paytype']=='3')//这里补充一个当为二次确认时,修改订单为未处理状态.
        {
            $info['status'] = 0;
        }
        else
        {
            $info['status'] = 1;
        }
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
        //合并生成订单
        $ordersn = Product::get_ordersn((string)$this->_typeid);
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
                $url = Common::get_web_url($info['webid']).'/tuan/show_'.$info['aid'].'.html';
                $this->request->redirect($url);
            }
        }
    }
}