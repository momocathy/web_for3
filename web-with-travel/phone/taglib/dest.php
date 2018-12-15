<?php
defined('SYSPATH') or die('No direct access allowed.');

/**
 * Created by Phpstorm.
 * User: netman
 * Date: 15-9-23
 * Time: 上午10:43
 * Desc: 目的地调用标签
 */
class Taglib_Dest
{

    /*
     * 获取广告
     * @param 参数
     * @return array
   */
    public static function query($params)
    {
        $default = array(
            'flag' => '',
            'row' => 8,
            'offset' => 0,
            'pid' => 0,
            'typeid' => 0
        );
        $params = array_merge($default, $params);
        extract($params);
        switch ($flag)
        {
            case 'top':
                $list = Model_Destinations::get_top($offset, $row);
                break;
            case 'next':
                $list = Model_Destinations::get_next($offset, $row, $pid,$typeid);
                break;
            case 'index_nav':
                $list = Model_Destinations::get_index_nav($offset, $row);
                break;
            case 'channel_nav':
                $list = Model_Destinations::get_channel_nav($offset, $row, $typeid);
                break;
            case 'hot':
                $list = Model_Destinations::get_hot_dest($typeid,$offset,$row,$destid);
                break;
            case 'dest':
                $list = Model_Destinations::get_dest($pid, $typeid, $offset, $row);
                break;
        }
        return $list;
    }

} 