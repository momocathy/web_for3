<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Jieban_Attr extends ORM
{
    /**
     * @desc 获取下级属性列表
     * @param $attrid
     * @return array
     */
    public static function get_attr_list($attrid)
    {
        if(empty($attrid))return null;
        $attrid = trim($attrid,',');
        $sql = "SELECT id,attrname FROM `sline_jieban_attr` WHERE id IN({$attrid}) AND isopen=1 and pid!=0 ORDER BY displayorder ASC";
        $arr = DB::query(1,$sql)->execute()->as_array();
        return $arr;
    }

}