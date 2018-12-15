<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Message
 * 消息提示
 */
class Controller_Message extends Stourweb_Controller
{
    //前置操作
 
    //显示错误
    public function action_index()
    {
        Common::message('');
        /*$msg = Common::session('jumpMsg');
        if (empty($msg))
        {
            $msg=array('error',$this->cmsurl);
        }
        $this->assign('message', $msg['message']);
        $this->assign('jumpUrl', $msg['jumpUrl']);
        $this->display('message');*/
    }
}