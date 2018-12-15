<?php
/**
 * Created by PhpStorm.
 * Author: netman
 * QQ: 87482723
 * Time: 15-9-26 上午10:04
 * Desc:属性调用标签
 */

class Taglib_Attr {

    /**
     * @param $params
     * @return mixed
     * @description 标签接口
     */
    public static function query($params)
    {
        $default=array(
            'row'=>'10',
            'flag'=>'',
            'offset'=>0,
            'groupid'=>0,
            'typeid'=>0,
            'limit'=>0
        );
        $params=array_merge($default,$params);
        extract($params);
        switch($flag)
        {
            case 'childitem':
                $arr = self::get_attr_bygroupid($typeid,$groupid,$offset,$row);
                break;
            case 'grouplist':
                $arr = self::grouplist($typeid,$offset,$row);
                break;

        }
        return $arr;

    }

    /**
     * 根据组名获取下级属性组
     * @param $typeid
     * @param $groupid
     * @param $offset
     * @param $limit
     * @return Array
     *
     */
    private  static function get_attr_bygroupid($typeid,$groupid,$offset,$row)
    {
        $arr = array();
        $attrtable = ORM::factory('model',$typeid)->get('attrtable');
        //排除通用模块和签证模块(读取属性)
        if(!empty($attrtable) && $typeid!=8 && $typeid<17)
        {

            $arr = ORM::factory($attrtable)
                ->where("pid=$groupid AND isopen=1")
                ->offset($offset)
                ->limit($row)
                ->get_all();

        }
        return $arr;
    }

    /**
     * @param $typeid
     * @param $offset
     * @param $row
     * @return Array
     */

    private static function grouplist($typeid,$offset,$row)
    {
        $arr = array();
        $attrtable = ORM::factory('model',$typeid)->get('attrtable');
        //排除通用模块和签证模块(读取属性)
        if(!empty($attrtable) && $typeid!=8 && $typeid<17)
        {
            $arr = ORM::factory($attrtable)
                ->where("pid=0 AND isopen=1")
                ->offset($offset)
                ->limit($row)
                ->get_all();

        }
        return $arr;

    }

}