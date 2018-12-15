<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Line
 * 线路控制器
 */
class Controller_Line extends Stourweb_Controller
{
    private $_typeid = 1;   //产品类型

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
        $this->display('line/index');
    }

    /**
     * 线路搜索页
     */
    public function action_list()
    {
        //参数处理
        $urlParam = $this->request->param('params');
        $destPy = 'all';
        $dayId=$priceId=$sortType=$keyword=$attrId=$startcityId=0;
        $params = explode('-', str_replace('/', '-', $urlParam));
        $count = count($params);
        switch ($count)
        {
            //目的地
            case 1:
                list($destPy) = $params;
                break;
            //关键字、属性
            case 8:
                list($destPy, $dayId, $priceId, $sortType, $keyword, $startcityId,$attrId,$page) = $params;
                break;
        }

        $keyword = Arr::get($_GET,'keyword');
        $page = $page < 1 ? 1 : $page;

        $destname =$destPy!='all' ? ORM::factory('destinations')->where("pinyin='$destPy'")->find()->get('kindname') : '目的地';
        //获取seo信息
        $seo = Model_Hotel::search_seo($destPy);
        $this->assign('seoinfo',$seo);
        $this->assign('destpy',Common::remove_xss($destPy));
        $this->assign('destname',$destname);
        $this->assign('dayid',Common::remove_xss($dayId));
        $this->assign('sorttype',Common::remove_xss($sortType));
        $this->assign('keyword',Common::remove_xss($keyword));
        $this->assign('attrid',Common::remove_xss($attrId));
        $this->assign('startcityid',Common::remove_xss($startcityId));
        $this->assign('priceid',Common::remove_xss($priceId));
        $this->assign('page',$page);
        $this->display('line/list');
    }

    /**
     * 线路搜索页(搜索初始页)
     */
    public function action_search()
    {
        $this->display('line/search');
    }

    /**
     * 线路预订
     */
    public function action_book()
    {
        $userinfo = Common::session('member');
        $productid = $this->params['id'];
        $info = ORM::factory('line',$productid)->as_array();
        $info['price'] = Model_Line::get_minprice($info['id']);;
        $this->assign('info',$info);
        $this->assign('userinfo',$userinfo);
        $this->display('line/book');
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
        //出行时间
        $startdate = Arr::get($_POST,'startdate');


        //成人数量
        $dingnum = Arr::get($_POST,'dingnum');
        //小孩数量
        $childnum = Arr::get($_POST,'childnum');
        //老人数量
        $oldnum = Arr::get($_POST,'oldnum');


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

        $info = ORM::factory('line')->where("id=$id")->find()->as_array();
        $suitArr = ORM::factory('line_suit')
            ->where("id=:suitid")
            ->param(':suitid',$suitid)
            ->find()
            ->as_array();
        $priceArr = ORM::factory('line_suit_price')->where("day=".strtotime($startdate)." and suitid=".$suitid)->find()->as_array();

        //库存判断
        $totalnum = $dingnum + $childnum + $oldnum;
        $storage = intval($priceArr['number']);
        if($storage!=-1 && $storage < $totalnum)
        {
            Common::message(array('message' => __("error_no_storage"), 'jumpUrl' =>$refer_url));
            exit;
        }


        if($suitArr['paytype']=='3')//这里补充一个当为二次确认时,修改订单为未处理状态.
        {
            $info['status'] = 0;
        }
        else
        {
            $info['status'] = 1;
        }
        $info['name'] = $info['title']."({$suitArr['suitname']})";
        $info['paytype'] = $suitarr['paytype'];
        $info['dingjin'] = intval($suitArr['dingjin']);
        $info['jifentprice'] = intval($suitArr['jifentprice']);
        $info['jifenbook'] = intval($suitArr['jifenbook']);
        $info['jifencomment'] = intval($suitArr['jifencomment']);
        $info['ourprice'] = intval($priceArr['adultprice']);
        $info['childprice'] = intval($priceArr['childprice']);
        $info['oldprice'] = intval($priceArr['oldprice']);
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
        $ordersn = Product::get_ordersn('01');
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
            'childnum'=>$childnum,
            'oldprice'=>$info['oldprice'],
            'oldnum'=>$oldnum,
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

            $orderInfo = Model_Member_Order::get_order_by_ordersn($ordersn);
            Model_Member_Order::add_tourer($orderInfo['id'],$_POST);
            //如果是立即支付则执行支付操作,否则跳转到产品详情页面
            if($info['status']==1)
            {
                $this->request->redirect('pub/pay/orderid/'.$orderInfo['id']);
            }
            else
            {
                $url = Common::get_web_url($info['webid']).'/lines/show_'.$info['aid'].'.html';
                $this->request->redirect($url);
            }

        }
    }

    /**
     * 线路内容页
     */
    public function action_show()
    {
        $id = Common::remove_xss($this->request->param('aid'));
        //线路详情
        $info = Model_Line::detail($id);
        //点击率加一
        Product::update_click_rate($aid, $this->_typeid);
        //seo
        $seoInfo = Product::seo($info);
        //产品图片
        $info['piclist'] = Product::pic_list($info['piclist']);
        //属性列表
        $info['attrlist'] = Model_Line::line_attr($info['attrid']);
        //最低价
        $info['price'] = Model_Line::get_minprice($info['id']);
        //出发城市
        $info['startcity'] = Model_Startplace::start_city($info['startcity']);
        //点评数
        $info['commentnum'] = Model_Comment::get_comment_num($info['id'], $this->_typeid);
        $info['satisfyscore'] = Model_Comment::get_score($info['id'], $this->_typeid, $info['satisfyscore'], $info['commentnum']);//满意度
        //销售数量
        $info['sellnum'] = Model_Member_Order::get_sell_num($info['id'], $this->_typeid) + intval($info['bookcount']);
        //产品编号
        $info['lineseries'] = Product::product_number($info['id'], '01');
        $this->assign('seoinfo', $seoInfo);
        $this->assign('info', $info);
        $this->display('line/show');
    }



    /**
     * 获取套餐人群
     */
    public function action_ajax_suit_people()
    {
      $out = array();
      $suitid = Arr::get($_GET,'suitid');
      $row = ORM::factory('line_suit',$suitid)->as_array();
      $group_arr = explode(',',$row['propgroup']);

        //获取最接近当前日期的报价
        $day = time();
        $ar = Model_Line_Suit_Price::get_price_byday($suitid,$day);
        if($ar)
        {

            $out['useday'] = date('Y-m-d',$ar[0]['day']);//当前使用日期.
            $out['storage'] = $ar[0]['number'];//库存

        }


      if(in_array(1,$group_arr))
      {
          $out['haschild'] = 1;
          $out['childprice'] = $ar[0]['childprice'] ? $ar[0]['childprice'] : 0;
      }
      if(in_array(2,$group_arr))
      {
            $out['hasadult'] = 1;
            $out['adultprice'] =$ar[0]['adultprice'] ? $ar[0]['adultprice'] : 0;
      }
      if(in_array(3,$group_arr))
      {
            $out['hasold'] = 1;
            $out['oldprice'] = $ar[0]['oldprice'] ? $ar[0]['oldprice'] : 0;
      }
      echo json_encode($out);
    }

    /**
     * 按天获取报价与库存.
     */
    public static function action_ajax_price_day()
    {
        $useday = strtotime(Arr::get($_GET,'useday'));
        $suitid = Arr::get($_GET,'suitid');
        $ar = Model_Line_Suit_Price::get_price_byday($suitid,$useday);
        echo json_encode($ar[0]);
    }

    /**
     * ajax请求 加载更多
     */
    public function action_ajax_line_more()
    {
        $urlParam = $this->request->param('params');
        $keyword = Arr::get($_GET,'keyword') ? Arr::get($_GET,'keyword') : '';
        $data = Model_Line::search_result($urlParam,$keyword);
        echo ($data);
    }
}