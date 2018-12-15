<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Hotel
 * @desc 总控制器
 */
class Controller_Destination extends Stourweb_Controller
{
    private $_typeid = 12;   //产品类型

    public function before()
    {
        parent::before();
        $channelname = Model_Nav::get_channel_name($this->_typeid);
        $this->assign('typeid', $this->_typeid);
        $this->assign('channelname', $channelname);
    }
    /**
     * 首页
     */
    public function action_index()
    {
         $seoinfo = Model_Nav::get_channel_seo($this->_typeid);
         $this->assign('seoinfo', $seoinfo);
         $this->display('destination/index');
    }

    public function action_main()
    {
        //参数处理
        $destpy = Common::remove_xss($this->request->param('pinyin'));

        $destinfo = Model_Destinations::get_dest_bypinyin($destpy);

        //获取seo信息
        $seo = Model_Destinations::search_seo($destpy, 0);
        $this->assign('seoinfo', $seo);
        $this->assign('destinfo', $destinfo);

        $this->display('destination/main');
    }

}