<?php
defined('SYSPATH') or die('No direct access allowed.');

class Model_Article_Attr extends ORM
{
    /**
     * @param $attrid
     * @return mixed
     * @desc 根据属性id返回属性数组.
     */
    public static function get_attr_list($attrid)
    {
       if(empty($attrid))return array();
       $sql = "SELECT id,attrname FROM `sline_article_attr` WHERE id IN($attrid) AND isopen=1 ORDER BY displayorder ASC";
       $arr = DB::query(1,$sql)->execute()->as_array();
       return $arr;
    }

}
