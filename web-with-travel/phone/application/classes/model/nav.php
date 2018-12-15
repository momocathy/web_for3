<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Nav extends ORM
{
    /**
     * @param $typeid 根据$typeid获取栏目信息
     * @return mixed
     */
    public static function get_channel_info($typeid)
    {
        $sql = "SELECT a.*,b.m_title,b.m_isopen FROM sline_nav as a LEFT JOIN sline_m_nav b ON a.id=b.navid WHERE a.typeid=$typeid and a.webid=0";
        $arr = DB::query(1,$sql)->execute()->as_array();
        return $arr[0];
    }

    /**
     * @param $typeid 栏目IDs
     * @return mixed 返回栏目名称
     */
    public static function get_channel_name($typeid)
    {
        $ar = self::get_channel_info($typeid);
        $channelname = $ar['m_title'] ? $ar['m_title'] : $ar['shortname'];
        return $channelname;
    }

    /**
     * @param $typeid
     * @return mixed 返回栏目优化标题等信息.
     */
    public static function get_channel_seo($typeid)
    {
        $sql = "SELECT seotitle,keyword,description,shortname FROM sline_nav WHERE typeid='$typeid' limit 1";
        $ar = DB::query(1,$sql)->execute()->as_array();
        $ar[0]['seotitle'] = !empty($ar[0]['seotitle']) ? $ar[0]['seotitle'] : $ar[0]['shortname'];
        return $ar[0];
    }

    /**
     * @param $typeid
     * @return int
     * 获取栏目的开启状态
     */
    public static function get_channel_isopen($typeid)
    {
        $sql = "SELECT b.m_isopen FROM `sline_nav` as a LEFT JOIN sline_m_nav b ON a.id=b.navid WHERE a.typeid=$typeid and a.webid=0";
        $arr = DB::query(1,$sql)->execute()->as_array();
        $row = $arr[0];
        return $row['m_isopen'] == 1 ? 1 : 0;
    }

    /**
     * @return int
     * 获取手机版开启的系统栏目
     */
    public static function get_all_m_channel()
    {
        $sql = "SELECT a.typeid FROM `sline_nav` as a LEFT JOIN sline_m_nav b ON a.id=b.navid WHERE b.m_isopen=1 and a.webid=0 AND a.typeid<=13";
        $arr = DB::query(1,$sql)->execute()->as_array();
        return $arr;
    }

    /**
     * @return array
     * 获取手机版系统栏目的开启状态与名称
     */

    public static function get_all_channel_info()
    {
        $out = array();
        $sql = "SELECT id,pinyin FROM `sline_model` ";
        $sql.= "WHERE id<17";
        $arr = DB::query(1,$sql)->execute()->as_array();
        foreach($arr as $row)
        {
            $info = array();
            $info['isopen'] = self::get_channel_isopen($row['id']);
            $info['channelname'] = self::get_channel_name($row['id']);
            $out[$row['pinyin']] = $info;
        }
        return $out;

    }
}