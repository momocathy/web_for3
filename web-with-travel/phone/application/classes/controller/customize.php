<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Customize
 * 私人定制控制器
 */
class Controller_Customize extends Stourweb_Controller
{
    private $_typeid = 14;   //产品类型

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
        $this->assign('seoinfo', $seoinfo);
        $this->display('customize/index');
    }

    /**
     * 验证码
     */
    public function action_ajax_check_code()
    {
        //验证码检测
        if (isset($_POST['code']))
        {
            $code = Arr::get($_POST, 'code');
            $result = (bool) (sha1(utf8::strtoupper($code)) === Session::instance()->get('captcha_response'));
            if ($result)
            {
                print_r('true');
                exit;
            }
            else
            {
                print_r('false');
                exit;
            }
        }
        else
        {
            print_r('false');
            exit;
        }
    }

    /**
     * 私人定制后台处理
     * @return string
     */
    public function action_ajax_do_add()
    {
        if(!$this->request->is_ajax())return '';
        $status = 0;
        //目的地
        $dest = Common::remove_xss(Arr::get($_POST, 'dest'));
        //出发地点
        $startplace = Common::remove_xss(Arr::get($_POST, 'startplace'));
        //出发时间
        $starttime = strtotime(Common::remove_xss(Arr::get($_POST, 'starttime')));
        $starttime = $starttime > 0 ? $starttime : '';
        //出行时长
        $days = Common::remove_xss(intval(Arr::get($_POST, 'days')));
        //成人数
        $adultnum = Common::remove_xss(intval(Arr::get($_POST, 'adultnum')));
        //儿童数
        $childnum = Common::remove_xss(intval(Arr::get($_POST, 'childnum')));
        //交通方式
        $planerank = Common::remove_xss(Arr::get($_POST, 'planerank'));
        //酒店星级
        $hotelrank = Common::remove_xss((Arr::get($_POST, 'hotelrank')));
        //需要房型
        $room = Common::remove_xss((Arr::get($_POST, 'room')));
        //用餐形式
        $food = Common::remove_xss((Arr::get($_POST, 'food')));
        //您的称呼
        $contactname = Common::remove_xss(Arr::get($_POST, 'contactname'));
        //性别
        $sex = Common::remove_xss(Arr::get($_POST, 'sex'));
        //所在地点
        $address = Common::remove_xss((Arr::get($_POST, 'address')));
        //手机号码
        $phone = Common::remove_xss((Arr::get($_POST, 'phone')));
        //E-mail
        $email = Common::remove_xss((Arr::get($_POST, 'email')));
        //合适的联系时间
        $contacttime = Common::remove_xss((Arr::get($_POST, 'contacttime')));
        //其他要求
        $content = Common::remove_xss((Arr::get($_POST, 'content')));
        //验证码
        $code = Common::remove_xss(Arr::get($_POST, 'code'));

        //验证码检测
        if (isset($code))
        {
            if (!Captcha::valid($code))
            {
                echo json_encode(array('status'=>$status, 'message'=>__("error_code")));
                exit;
            }
            Common::session('captcha_response', null);
        }
        else
        {
            echo json_encode(array('status'=>$status, 'message'=>__("error_code_not_empty")));
            exit;
        }

        $validataion = Validation::factory($this->request->post());
        $validataion->rule('dest', 'not_empty');
        $validataion->rule('startplace', 'not_empty');
        $validataion->rule('starttime', 'not_empty');
        $validataion->rule('starttime', 'date');
        $validataion->rule('days', 'digit');
        $validataion->rule('adultnum', 'digit');
        $validataion->rule('childnum', 'digit');
        $validataion->rule('planerank', 'not_empty');
        $validataion->rule('hotelrank', 'not_empty');
        $validataion->rule('room', 'not_empty');
        $validataion->rule('food', 'not_empty');
        $validataion->rule('contactname', 'not_empty');
        $validataion->rule('sex', 'not_empty');
        $validataion->rule('address', 'not_empty');
        $validataion->rule('phone', 'not_empty');
        $validataion->rule('phone', 'phone');
        $validataion->rule('email', 'not_empty');
        $validataion->rule('email', 'email');
        $validataion->rule('contacttime', 'not_empty');

        if (!$validataion->check())
        {
            $error = $validataion->errors();
            $keys = array_keys($validataion->errors());
            if($keys[0] == 'phone')
            {
                echo json_encode(array('status'=>$status, 'message'=>__("error_user_phone")));
            }
            elseif($keys[0] == 'email')
            {
                echo json_encode(array('status'=>$status, 'message'=>__("error_user_email")));
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
                echo json_encode(array('status'=>$status, 'message'=>__("error_customize_{$keys[0]}_{$error[$keys[0]][0]}")));
            }
            exit;
        }
        list($insert_id, $total_rows) = DB::insert('customize', array('dest', 'startplace', 'starttime', 'days', 'adultnum', 'childnum', 'planerank', 'hotelrank', 'room', 'food', 'addtime', 'contactname', 'sex', 'address', 'phone', 'email', 'contacttime'))
            ->values(array($dest, $startplace, $starttime, $days, $adultnum, $childnum, $planerank, $hotelrank, $room, $food, time(), $contactname, $sex, $address, $phone, $email, $contacttime))
            ->execute();
        if ($total_rows)
        {
            $status = 1;
            echo json_encode(array('status'=>$status, 'message'=>__("success_customize_add_insert"), 'id'=> $insert_id));
        }
        else
        {
            echo json_encode(array('status'=>$status, 'message'=>__("error_customize_add_insert")));
        }
    }
}

