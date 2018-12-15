<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Car
 * 租车控制器
 */
class Controller_Car extends Stourweb_Controller
{
    private $_typeid = 3;   //产品类型

    public function before()
    {
        parent::before();

        $channelname = Model_Nav::get_channel_name($this->_typeid);
        $this->assign('typeid', $this->_typeid);
        $this->assign('channelname', $channelname);
    }

    /**
     * 租车首页
     */
    public function action_index()
    {
        $seoinfo = Model_Nav::get_channel_seo($this->_typeid);
        $this->assign('seoinfo', $seoinfo);
        $this->display('car/index');
    }

    /**
     * 租车搜索页(搜索初始页)
     */
    public function action_search()
    {
        $this->display('car/search');
    }

    /**
     * 租车列表
     */
    public function action_list()
    {
        $uri = $this->request->param('params');
        $uriArr = $this->_explode_url($uri);
        $destname =$uriArr['destPy']!='all' ? ORM::factory('destinations')->where("pinyin='{$uriArr['destPy']}'")->find()->get('kindname') : '目的地';
        //获取seo信息
        $seo = Model_Car::search_seo($uriArr['destPy']);
        $this->assign('seoinfo', $seo);
        $this->assign('destname', $destname);
        $this->assign('destpy', $uriArr['destPy']);
        $this->assign('attrid', $uriArr['attrid']);
        $this->assign('kindid', $uriArr['kindid']);
        $this->assign('sorttype', $uriArr['sorttype']);
        $this->assign('keyword', $uriArr['keyword']);
        $this->assign('page', $uriArr['page']);
        $this->display('car/list');
    }

    /**
     * ajax请求 加载更多
     */
    public function action_ajax_car_more()
    {
        $uri = $this->request->param('params');
        $uriArr = $this->_explode_url($uri);
        $data = Model_Car::search_result($uriArr);
        echo ($data);
    }

    /**
     * 分隔URL参数
     * @return mixed
     */
    private function _explode_url($uri)
    {
        $params = explode('-', str_replace('/', '-', Common::remove_xss($uri)));
        $data['startplaceid']=$data['kindid']=$data['sorttype']=$data['attrid']=0;
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
            //关键字、属性
            case 5:
                list($data['destPy'], $data['startplaceid'], $data['kindid'],  $data['sorttype'], $data['attrid']) = $params;
                break;
            //关键字、属性
            case 6:
                list($data['destPy'], $data['startplaceid'], $data['kindid'],  $data['sorttype'], $data['attrid'], $data['page']) = $params;
                break;
        }
        //分页
        $data['page'] = empty($data['page']) ? 1 : $data['page'];
        //关键字
        $data['keyword'] = empty($_GET['keyword']) ? '' : $_GET['keyword'];
        return $data;
    }

    /**
     * 租车详情页
     */
    public function action_show()
    {
        $aid = Common::remove_xss($this->request->param('aid'));
        $info = ORM::factory('car')
            ->where('webid','=',$GLOBALS['sys_webid'])
            ->and_where('aid','=',$aid)
            ->find()
            ->as_array();
        //扩展字段信息
        $extend_info = ORM::factory('car_extend_field')
            ->where("productid=".$info['id'])
            ->find()
            ->as_array();
        //点击率加一
        Product::update_click_rate($aid, $this->_typeid);

        $seoinfo = Product::seo($info);
        $info['piclist']=Product::pic_list($info['piclist']);
        $info['commentnum'] = Model_Comment::get_comment_num($info['id'],$this->_typeid); //评论次数
        $info['satisfyscore'] = Model_Comment::get_score($info['id'], $this->_typeid, $info['satisfyscore'], $info['commentnum']);//满意度
        $info['sellnum'] = Model_Member_Order::get_sell_num($info['id'], $this->_typeid) + intval($info['bookcount']); //销售数量
        $info['carkindname'] = Model_Car_Kind::get_carkindname($info['carkindid']);
        $info['attrlist'] = Model_Car_attr::get_attr_list($info['attrid']);
        $info['price'] = Model_Car::get_car_suit_price($info['aid'], $info['webid'] , $info['id']);
        $this->assign('seoinfo',$seoinfo);
        $this->assign('info',$info);
        $this->assign('extendinfo',$extend_info);
        $this->display('car/show');
    }

    /**
     * 租车预订页
     */
    public function action_book()
    {
        $userinfo = Common::session('member');
        $productid = Common::remove_xss($this->params['id']);
        $info = ORM::factory('car',$productid)->as_array();
        $info['price'] = Model_Car::get_car_suit_price($info['aid'], $info['webid'] , $info['id']);
        $this->assign('info',$info);
        $this->assign('userinfo',$userinfo);
        $this->display('car/book');
    }

    /**
     * 获取租车开始和结束日期价格
     */
    public function action_ajax_range_price()
    {
        $startdate = Arr::get($_GET,'startdate');
        $enddate = Arr::get($_GET,'leavedate');
        $suitid = Arr::get($_GET,'suitid');
        $dingnum = Arr::get($_GET,'dingnum');
        $price = Model_Car::suit_range_price($suitid,$startdate,$enddate,$dingnum);
        echo json_encode(array('price'=>$price));
    }

    /**
     * 获取套餐可预订的最小日期.
     */
    public function action_ajax_mindate_book()
    {
        $suitid = Arr::get($_GET,'suitid');
        $day = Model_Car::suit_mindate_book($suitid);

        echo json_encode( array(
            'startdate'=>date('Y-m-d',$day),
            'enddate'=>date('Y-m-d',strtotime("+1 day",$day))
        ));
    }

    /**
     * 创建订单
     */
    public function action_create()
    {
        $refer_url = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : $this->cmsurl;
        //套餐id
        $suitid = Arr::get($_POST,'suitid');
        //联系人
        $linkman = Arr::get($_POST,'linkman');
        //手机号
        $linktel = Arr::get($_POST,'linktel');
        $linkidcard = Arr::get($_POST,'linkidcard');
        //备注信息
        $remark = Arr::get($_POST,'remark');
        //产品id
        $id = Arr::get($_POST,'productid');
        //开始时间
        $startdate = Arr::get($_POST,'startdate');
        //结束时间
        $leavedate = Arr::get($_POST,'leavedate');
        //数量
        $dingnum = Arr::get($_POST,'dingnum');

        //验证部分
        $validataion = Validation::factory($_POST);
        $validataion->rule('linktel', 'not_empty');
        $validataion->rule('linktel','phone');
        $validataion->rule('linkman', 'not_empty');

        if (!$validataion->check())
        {
            $error = $validataion->errors();
            $keys = array_keys($validataion->errors());
            Common::message(array('message' => __("error_{$keys[0]}_{$error[$keys[0]][0]}"), 'jumpUrl' =>$refer_url));
        }

        $info = ORM::factory('car')->where("id=$id")->find()->as_array();
        $suitArr = ORM::factory('car_suit')->where("id=:suitid")->param(':suitid',$suitid)->find()->as_array();
        $priceArr = ORM::factory('car_suit_price')->where('day=' . strtotime($startdate) . " and suitid={$suitid}")->find()->as_array();
        //检测库存
        $totalnum = $dingnum;
        $storage = intval($priceArr['number']);
        if($storage!=-1 && $storage < $totalnum)
        {
            Common::message(array('message' => __("error_no_storage"), 'jumpUrl' =>$refer_url));
            exit;
        }
        //这里补充一个当为二次确认时,修改订单为未处理状态.
        if($suitArr['paytype']=='3')
        {
            $info['status'] = 0;
        }
        else
        {
            $info['status'] = 1;
        }
        $info['name'] = $info['title']."({$suitArr['suitname']})";
        $info['paytype'] = $suitArr['paytype'];
        $info['dingjin'] = intval($suitArr['dingjin']);
        $info['jifentprice'] = intval($suitArr['jifentprice']);
        $info['jifenbook'] = intval($suitArr['jifenbook']);
        $info['jifencomment'] = intval($suitArr['jifencomment']);
        $info['ourprice'] = intval($priceArr['adultprice']);
        $info['childprice'] = 0;
        $info['usedate']= $startdate ;
        $info['departdate'] = $leavedate;

        $userinfo = Common::session('member');

        if(!isset($userinfo['mid']))//如果未登陆
        {

            $sql = "SELECT * FROM `sline_member` WHERE mobile='$linktel' LIMIT 1";
            $row = DB::query(1,$sql)->execute()->as_array();
            if(!empty($row[0]['mid']))
            {
                $mid = $row[0]['mid'];
            }
            else //如果不存在则创建新用户
            {
                $model = ORM::factory('member');
                $model->mobile = $linktel;
                $model->pwd = md5($linktel);
                $model->nickname = substr_replace($linktel,'***',3,3);
                $model->save();
                if($model->saved()) //注册成功
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
        $ordersn = Product::get_ordersn('03');
        $arr = array(
            'ordersn'=>$ordersn,
            'webid'=>0,
            'typeid'=>$this->_typeid,
            'productautoid'=>$id,
            'productaid'=>$info['aid'],
            'productname'=>$info['name'],
            'litpic'=>$info['litpic'],
            'price'=>$info['ourprice'],
            'childprice'=>$info['childprice'],
            'jifentprice'=>$info['jifentprice'],
            'jifenbook'=>$info['jifenbook'],
            'jifencomment'=>$info['jifencomment'],
            'paytype'=>$info['paytype'],
            'dingjin'=>$info['dingjin'],
            'usedate'=>$info['usedate'],
            'departdate'=>$info['departdate'],
            'addtime'=>time(),
            'memberid'=>$mid,
            'dingnum'=>$dingnum,
            'childnum'=>0,
            'oldprice'=>0,
            'oldnum'=>0,
            'linkman'=>$linkman,
            'linktel'=>$linktel,
            'linkidcard'=>$linkidcard,
            'suitid'=> $suitid,
            'remark'=> $remark,
            'status'=>$info['status'] ? $info['status'] : 0
        );
        //添加订单
        if(Product::add_order($arr))
        {
            $orderInfo = Model_Member_Order::get_order_by_ordersn($ordersn);//var_dump($orderInfo);
            if($info['status'] == 1)
            {
                $this->request->redirect('pub/pay/orderid/'.$orderInfo['id']);
            }
            else
            {
                $url = Common::get_web_url($info['webid']).'/cars/show_'.$info['aid'].'.html';
                $this->request->redirect($url);
            }

        }
    }
}