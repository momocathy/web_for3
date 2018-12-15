<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Jieban 结伴
 */
class Controller_Jieban extends Stourweb_Controller
{
    private $_typeid = 11;   //产品类型

    public function before()
    {
        parent::before();

        $channelname = Model_Nav::get_channel_name($this->_typeid);
        $this->assign('typeid', $this->_typeid);
        $this->assign('channelname', $channelname);
    }

    /**
     * 结伴首页
     */
    public function action_index()
    {
        $seoinfo = Model_Nav::get_channel_seo($this->_typeid);
        $page = Common::remove_xss(intval(Arr::get($_GET, 'page')));
        $page = $page < 1 ? 1 : $page;
        $this->assign('page', $page);
        $this->assign('typeid', $this->_typeid);
        $this->assign('seoinfo', $seoinfo);
        $this->display('jieban/index');
    }

    /**
     * 结伴首页  查看更多
     */
    public function action_ajax_jieban_more()
    {
        $page = Common::remove_xss(intval(Arr::get($_GET,'page')));
        $data = Model_Jieban::get_jieban_list($page, 10);
        echo $data;
    }

    /**
     * 结伴详情页
     */
    public function action_show()
    {
        $aid = Common::remove_xss($this->request->param('aid'));
        //结伴详情
        $info = Model_Jieban::detail($aid);
        //点击率加一
        Product::update_click_rate($aid, $this->_typeid);
        
        $info['memo'] = $info['memo'] ? $info['memo'] : $info['title'];
        //发布人
        $memberArr = Model_Member::get_member_byid($info['memberid']);
        $info['member']['name'] = $memberArr['nickname'] ? $memberArr['nickname'] : $memberArr['nickname'];
        $info['member']['litpic'] = $memberArr['litpic'] ? $memberArr['litpic'] : Common::nopic();
        //目的地
        $info['kindnamelist'] = Model_Jieban::get_kindnamelist($info['kindlist']);
        //活动类型
        $info['attrlist'] = Model_Jieban_Attr::get_attr_list($info['attrid']);
        //加入人数
        $joinnumArr = Model_Jieban_Join::get_join_number($info['id']);
        $info['joinnum'] = intval($joinnumArr['adultnum']) + intval($joinnumArr['childnum']);
        //活动类型
        $info['recommendlist'] = Model_Jieban::get_line_recommend($info['kindlist'], $info['day'], 3);
        foreach ($info['recommendlist'] as &$v)
        {
            $v['litpic'] = $v['litpic'] ? $v['litpic'] : Common::nopic();
            $v['url'] = Common::get_web_url($v['webid']) . "/lines/show_{$v['aid']}.html";
        }
        //seo
        $seoInfo = Model_Jieban::seo($info);
        $this->assign('seoinfo', $seoInfo);
        $this->assign('info', $info);
        $this->display('jieban/show');
    }

    /**
     * 加入结伴
     */
    public function action_join()
    {
        $id = Common::remove_xss($this->params['id']);
        $info = Model_Jieban::detail($id);
        $seoInfo = Model_Jieban::seo($info);
        //短信验证是否开启
        $sms_msg = DB::select()->from('sms_msg')->where("msgtype='reg_findpwd'")->execute()->current();
        $this->assign('isopen', $sms_msg['isopen']);
        $this->assign('seoinfo', $seoInfo);
        $this->assign('info', $info);
        $this->display('jieban/join');
    }

    /**
     * 加入结伴处理
     */
    public function action_ajax_do_join()
    {
        if(!$this->request->is_ajax())return '';
        $status = 0;

        $id = Common::remove_xss(intval(Arr::get($_POST, 'id')));
        //联系人
        $linkman = Common::remove_xss(Arr::get($_POST, 'linkman'));
        //电话
        $mobile = Common::remove_xss(Arr::get($_POST, 'mobile'));
        //验证码
        $msg = Common::remove_xss((Arr::get($_POST, 'msg')));
        //成人数
        $adultnum = Common::remove_xss(intval(Arr::get($_POST, 'adultnum')));
        //儿童数
        $childnum = Common::remove_xss(intval(Arr::get($_POST, 'childnum')));

        if (isset($msg))
        {
            //短信验证是否开启
            $sms_msg = DB::select()->from('sms_msg')->where("msgtype='reg_findpwd'")->execute()->current();
            if($sms_msg['isopen'])
            {
                $result =  (bool) Common::session('msg_code') == $msg;
            }
            else
            {
                $result = (bool) (sha1(utf8::strtoupper($msg)) === Session::instance()->get('captcha_response'));
            }
            if (!$result)
            {
                echo json_encode(array('status'=>$status, 'message'=>__("error_code")));
                exit;
            }
            //清空验证码
            if($sms_msg['isopen'])
            {
                Common::session('msg_code', null);
            }
            else
            {
                Common::session('captcha_response', null);
            }
        }
        else
        {
            echo json_encode(array('status'=>$status, 'message'=>__("error_code_not_empty")));
            exit;
        }
        //检测用户是否存在
        $userinfo = Common::session('member');
        $mid = $userinfo['mid'];
        if (!isset($userinfo['mid']))
        {
            $user = Model_Member::member_find($mobile);
            if (empty($user))
            {
                $user = Model_Member::register(array('mobile' => $mobile, 'pwd' => md5($mobile)));
                $mid = $user[0];
            }
            else
            {
                $mid = $user['mid'];
            }
        }

        //检查用户重复加入结伴
        $join = Model_Jieban::repeat_join($mid, $id);
        if (!empty($join))
        {
            echo json_encode(array('status'=>$status, 'message'=>__("error_repeat_join")));
            exit;
        }

        $validataion = Validation::factory($this->request->post());
        $validataion->rule('linkman', 'not_empty');
        $validataion->rule('mobile', 'phone');
        $validataion->rule('adultnum', 'digit');
        $validataion->rule('childnum', 'digit');
        if (!$validataion->check())
        {
            $error = $validataion->errors();
            $keys = array_keys($validataion->errors());
            if($keys[0] == 'mobile')
            {
                echo json_encode(array('status'=>$status, 'message'=>__("error_user_phone")));
            }
            elseif($keys[0] == 'adultnum')
            {
                echo json_encode(array('status'=>$status, 'message'=>__("error_adultnum_digit")));
            }
            elseif($keys[0] == 'childnum')
            {
                echo json_encode(array('status'=>$status, 'message'=>__("error_childnum_digit")));
            }
            else
            {
                echo json_encode(array('status'=>$status, 'message'=>__("error_{$keys[0]}_{$error[$keys[0]][0]}")));
            }
            exit;
        }
        list($insert_id, $total_rows) = DB::insert('jieban_join', array('jiebanid', 'linkman', 'mobile', 'memberid', 'adultnum', 'childnum', 'addtime'))
            ->values(array($id, $linkman, $mobile, $mid, $adultnum, $childnum, time()))
            ->execute();


        if ($total_rows)
        {
            $status = 1;
            echo json_encode(array('status'=>$status, 'message'=>__("success_jieban_join_insert")));
        }
        else
        {
            echo json_encode(array('status'=>$status, 'message'=>__("error_jieban_join_insert")));
        }
    }

    /**
     * 发布结伴
     */
    public function action_add()
    {
        $seoinfo = Model_Nav::get_channel_seo($this->_typeid);
        //短信验证是否开启
        $sms_msg = DB::select()->from('sms_msg')->where("msgtype='reg_findpwd'")->execute()->current();
        $this->assign('isopen', $sms_msg['isopen']);
        $this->assign('seoinfo', $seoinfo);
        $this->display('jieban/add');
    }

    /**
     * 我要结伴后端处理
     * @return string
     */

    public function action_ajax_do_add()
    {
        if(!$this->request->is_ajax())return '';
        $status = 0;
        //目的地
        $kindlist = Common::remove_xss(Arr::get($_POST, 'kindlist'));
        //主目的地
        $dest_mainid = Common::remove_xss(intval(Arr::get($_POST, 'dest_mainid')));
        //子目的地
        $dest_childid = Common::remove_xss(intval(Arr::get($_POST, 'dest_childid')));
        //用户自定义目的地
        $userdest = Common::remove_xss(Arr::get($_POST, 'userdest'));
        $userdest = $userdest ? $userdest : 'null';
        //标题
        $title = Common::remove_xss(Arr::get($_POST, 'title'));
        //出发时间
        $startdate = Common::remove_xss(Arr::get($_POST, 'startdate'));
        //出行时间偏差
        $vartime = Common::remove_xss(intval(Arr::get($_POST, 'vartime')));
        //出行天数
        $day = Common::remove_xss(intval(Arr::get($_POST, 'day')));
        //成人数
        $adultnum = Common::remove_xss(intval(Arr::get($_POST, 'adultnum')));
        //儿童数
        $childnum = Common::remove_xss(intval(Arr::get($_POST, 'childnum')));
        //行程需求
        $attrid = Common::remove_xss(intval(Arr::get($_POST, 'attrid')));
        //自定义行程需求
        $usertheme = Common::remove_xss(Arr::get($_POST, 'usertheme'));
        //手机号码
        $mobile = Common::remove_xss(Arr::get($_POST, 'mobile'));
        //验证码
        $msg = Common::remove_xss((Arr::get($_POST, 'msg')));

        if (isset($msg))
        {
            //短信验证是否开启
            $sms_msg = DB::select()->from('sms_msg')->where("msgtype='reg_findpwd'")->execute()->current();
            if($sms_msg['isopen'])
            {
                $result =  (bool) Common::session('msg_code') == $msg;
            }
            else
            {
                $result = (bool) (sha1(utf8::strtoupper($msg)) === Session::instance()->get('captcha_response'));
            }
            if (!$result)
            {
                echo json_encode(array('status'=>$status, 'message'=>__("error_code")));
                exit;
            }
            //清空验证码
            if($sms_msg['isopen'])
            {
                Common::session('msg_code', null);
            }
            else
            {
                Common::session('captcha_response', null);
            }
        }
        else
        {
            echo json_encode(array('status'=>$status, 'message'=>__("error_code_not_empty")));
            exit;
        }

        //检测用户是否存在
        $userinfo = Common::session('member');
        $mid = $userinfo['mid'];
        if (!isset($userinfo['mid']))
        {
            $user = Model_Member::member_find($mobile);
            if (empty($user))
            {
                $user = Model_Member::register(array('mobile' => $mobile, 'pwd' => md5($mobile)));
                $mid = $user[0];
            }
            else
            {
                $mid = $user['mid'];
            }
        }

        $validataion = Validation::factory($this->request->post());
        $validataion->rule('kindlist', 'not_empty');
        $validataion->rule('dest_mainid', 'not_empty');
        $validataion->rule('title', 'not_empty');
        $validataion->rule('startdate', 'not_empty');
        $validataion->rule('startdate', 'date');
        $validataion->rule('vartime', 'digit');
        $validataion->rule('day', 'digit');
        $validataion->rule('mobile', 'not_empty');
        $validataion->rule('mobile', 'phone');
        $validataion->rule('adultnum', 'digit');
        $validataion->rule('childnum', 'digit');
        if (!$validataion->check())
        {
            $error = $validataion->errors();
            $keys = array_keys($validataion->errors());
            if($keys[0] == 'mobile')
            {
                echo json_encode(array('status'=>$status, 'message'=>__("error_user_phone")));
            }
            else
            {
                echo json_encode(array('status'=>$status, 'message'=>__("error_{$keys[0]}_{$error[$keys[0]][0]}")));
            }
            exit;
        }
        list($insert_id, $total_rows) = DB::insert('jieban', array('kindlist', 'dest_mainid', 'dest_childid', 'day', 'attrid', 'adultnum', 'childnum', 'vartime', 'startdate', 'addtime', 'memberid', 'userdest', 'usertheme', 'title'))
            ->values(array($kindlist, $dest_mainid, $dest_childid, $day, $attrid, $adultnum, $childnum, $vartime, $startdate, time(), $mid, $userdest, $usertheme, $title))
            ->execute();
        if ($total_rows)
        {
            $status = 1;
            echo json_encode(array('status'=>$status, 'message'=>__("success_jieban_add_insert"), 'id'=> $insert_id));
        }
        else
        {
            echo json_encode(array('status'=>$status, 'message'=>__("error_jieban_add_insert")));
        }
    }
}

