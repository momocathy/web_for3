<?php defined('SYSPATH') or die('No direct script access.');



/**

 * Class Controller_Spot 门票景点

 */

class Controller_Spot extends Stourweb_Controller

{

    private $_typeid = 5;   //产品类型

    

    public function before()

    {

        parent::before();

        $channelname = Model_Nav::get_channel_name($this->_typeid);

        $this->assign('typeid',$this->_typeid);

        $this->assign('channelname',$channelname);

    }

    /*

     * 景点首页

     * */

    public function  action_index()

    {

       $seoinfo = Model_Nav::get_channel_seo($this->_typeid);

       $this->assign('seoinfo',$seoinfo);

       $this->display('spot/index');

    }

    /*

     * 列表页面

     * */



    public function action_list()

    {

        //参数处理

        $urlParam = $this->request->param('params');



        $destPy = 'all';

        $priceId=$sortType=$keyword=$attrId=$page=0;

        $params = explode('-', str_replace('/', '-', $urlParam));

        $count = count($params);

        switch ($count)

        {

            //目的地

            case 1:

                list($destPy) = $params;

                break;

            //关键字、属性

            case 6:

                list($destPy,  $priceId, $sortType, $keyword, $attrId,$page) = $params;

                break;

        }

        $keyword = Arr::get($_GET,'keyword');

        $destname =$destPy!='all' ? ORM::factory('destinations')->where("pinyin='$destPy'")->find()->get('kindname') : '目的地';



        $page = empty($page) ? 1 : $page;

        //获取seo信息

        $seo = Model_Hotel::search_seo($destPy);

        $this->assign('seoinfo',$seo);

        $this->assign('destpy',Common::remove_xss($destPy));

        $this->assign('destname',$destname);

        $this->assign('sorttype',Common::remove_xss($sortType));

        $this->assign('keyword',Common::remove_xss($keyword));

        $this->assign('attrid',Common::remove_xss($attrId));

        $this->assign('priceid',Common::remove_xss($priceId));

        $this->assign('page', $page);

        $this->display('spot/list');

    }



    /*

     * 景点/门票详细页

     * */

    public function action_show()

    {

        $aid = $this->request->param('aid');

        $row = ORM::factory('spot')

            ->where("webid=".$GLOBALS['sys_webid']." AND aid='$aid'")

            ->find()

            ->as_array();

        //点击率加一

        Product::update_click_rate($aid, $this->_typeid);

        //扩展字段信息

        $extend_info = Model_Spot::get_extend_info($row['id']);

        $seoinfo = Product::seo($row);

        $priceArr = Model_Spot::get_minprice($row['id']);

        $row['hasticket'] = Model_Spot::has_ticket($row['id']);

        $row['piclist']=Product::pic_list($row['piclist']);

        $row['price'] = $priceArr['price'];

        $row['sellprice'] = $priceArr['sellprice'];

        $row['attrlist'] = Model_Spot_Attr::get_attr_list($row['attrid']);//属性列表.

        $row['commentnum'] = Model_Comment::get_comment_num($row['id'],5); //评论次数

        $row['satisfyscore'] =(int) Model_Comment::get_score($row['id'], $this->_typeid, $row['satisfyscore'], $row['commentnum']);//满意度

        $row['sellnum'] = Model_Member_Order::get_sell_num($row['id'],2)+intval($v['bookcount']); //销售数量

        $this->assign('seoinfo',$seoinfo);

        $this->assign('info',$row);

        $this->assign('extendinfo',$extend_info);

        $this->display('spot/show');

    }



    /*

     * 门票预订

     * */

    public function action_book()

    {

        $userinfo = Common::session('member');

        $productid = Common::remove_xss($this->params['id']);

        $info = ORM::factory('spot',$productid)->as_array();

        $priceArr = Model_Spot::get_minprice($info['id']);

        $info['price'] = $priceArr['price'];

        $this->assign('info',$info);

        $this->assign('userinfo',$userinfo);

        $this->display('spot/book');

    }



    /*

     * 创建订单

     * */



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

        //使用时间

        $usedate = Arr::get($_POST,'usedate');



        //预订数量

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



        $info = ORM::factory('spot')->where("id=$id")->find()->as_array();

        $suitArr = ORM::factory('spot_ticket')

            ->where("id=:suitid")

            ->param(':suitid',$suitid)

            ->find()

            ->as_array();



        if($suitArr['paytype']=='3')//这里补充一个当为二次确认时,修改订单为未处理状态.

        {

            $info['status'] = 0;

        }

        else

        {

            $info['status'] = 1;

        }

        $info['name'] = $info['title']."({$suitArr['title']})";

        $info['paytype'] = $suitarr['paytype'];

        $info['dingjin'] = intval($suitArr['dingjin']);

        $info['jifentprice'] = intval($suitArr['jifentprice']);

        $info['jifenbook'] = intval($suitArr['jifenbook']);

        $info['jifencomment'] = intval($suitArr['jifencomment']);

        $info['ourprice'] = intval($suitArr['ourprice']);

        $info['childprice'] = 0;

        $info['usedate']= $usedate ;

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

        $ordersn = Product::get_ordersn('05');

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

            'departdate'=>'',

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

            $sql = "SELECT id FROM `sline_member_order` WHERE ordersn='$ordersn'";

            $ar = DB::query(1,$sql)->execute()->as_array();

            if($info['status'] == 1)
            {

               $this->request->redirect('pub/pay/orderid/'.$ar[0]['id']);
            }
            else
            {
                $url = Common::get_web_url($info['webid']).'/spots/show_'.$info['aid'].'.html';
                $this->request->redirect($url);
            }

        }

    }





    /*

     * 景点搜索页(搜索初始页)

     */

    public function action_search()

    {

        $this->display('spot/search');



    }



    /**

     * ajax请求 加载更多

     */

    public function action_ajax_spot_more()

    {

        $urlParam = $this->request->param('params');



        $keyword = Arr::get($_GET,'keyword') ? Arr::get($_GET,'keyword') : '';



        $data = Model_Spot::search_result($urlParam,$keyword);



        echo $data;



    }

}