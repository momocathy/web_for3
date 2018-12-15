<?php
defined('SYSPATH') or die('No direct access allowed.');

class Model_Member extends ORM
{
    //对应数据库
    protected $_table_name = 'member';
    protected $_primary_key = 'mid';

    /*
     * 新增用户
     * @param array $data
     * return mixed
     */
    public static function register($data)
    {
        //数据验证
        //检查账号
        $user = isset($data['mobile']) ? $data['mobile'] : $data['email'];
        $user = self::member_find($user);
        if (!empty($user))
        {
            return __('error_member_exists');
        }
        //添加
        $result = DB::insert('member', array_keys($data))->values(array_values($data))->execute();
        return $result[1] > 0 ? $result : __('error_member_insert');
    }

    /*
     * 查找用户
     * @param string $user 用户账号
     * return array
     */
    public static function member_find($user, $pwd = null)
    {
        if (strpos($user, '@'))
        {
            $where = "email='{$user}'";
        }
        else
        {
            $where = "mobile='{$user}'";
        }
        if (!is_null($pwd))
        {
            $where .= " and pwd='" . md5($pwd) . "'";
        }
        $result = DB::select()->from('member')->where($where)->execute()->as_array();
        if (!empty($result))
        {
            $result = $result[0];
        }
        return $result;
    }

    /**
     * 根据会员id获取用户信息
     * @param $mid
     * @return array
     */
    public static function get_member_byid($mid)
    {
        if ($mid)
        {
            $memberinfo = ORM::factory('member', $mid)->as_array();
            return $memberinfo;
        }

    }

    /**
     * 积分处理
     * @param $mid
     * @param $jifen 积分
     * @param int $type 1：使用 2：获取
     * @return object
     */
    public static function operate_jifen($mid, $jifen, $type = 1)
    {
        if (empty($jifen))
        {
            return;
        }
        $jifen = $type == 1 ? -$jifen : $jifen;
        $sql = "UPDATE sline_member SET jifen=jifen+({$jifen}) ";
        $sql .= "WHERE mid={$mid}";
        $row = DB::query(Database::UPDATE, $sql)->execute();
        return $row;
    }

    /**
     * 写入session
     * @param $member 会员详细信息
     * @param $user 登录账号手机或邮箱
     */
    public static function write_session($member, $user = null)
    {
        //登录送积分
        Model_Member::operate_jifen($member['mid'], $GLOBALS['cfg_login_jifen'], 2);
        Product::add_jifen_log($member['mid'], "登录赠送积分：{$GLOBALS['cfg_login_jifen']}分", $GLOBALS['cfg_login_jifen'], 2);
        if(is_null($user)){
            $user=empty($member['email'])?$member['mobile']:$member['email'];
        }
        //昵称
        if (empty($member['nickname']) && !empty($user))
        {
            $member['nickname'] = substr_replace($user, '****', floor(strlen($user) / 2) - 2, 4);
        }
        //没有会员图片
        if (empty($member['litpic']))
        {
            $member['litpic'] = Common::member_nopic();
        }
        //登录信息写入seesion
        Common::session('member', array('mid' => $member['mid'], 'nickname' => $member['nickname'], 'litpic' => $member['litpic']));
    }
}
