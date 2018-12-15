<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Index extends Stourweb_Controller{

    public function before()
    {
        parent::before();
    }

    //首页
    public function action_index()
    {
        //seo信息
        $seoinfo = array(
            'seotitle' => $GLOBALS['cfg_indextitle'],
            'keyword' => $GLOBALS['cfg_keywords'],
            'description' => $GLOBALS['cfg_description']
        );

        //获取栏目名称与开启状态
        $channel = Model_Nav::get_all_channel_info();
        $this->assign('channel',$channel);
        $this->assign('seoinfo',$seoinfo);
        $this->display('index');
    }
}