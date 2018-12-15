<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Jieban_Join extends ORM
{
    /**
     * 获取加入人数
     * @param $id
     * @param int $memberid
     * @return int
     */
    public static function get_join_number($id,$memberid=0)
    {
        $sql = "select SUM(adultnum) as adultnum,SUM(childnum) as childnum from sline_jieban_join where jiebanid='$id'";
        $sql.= $memberid ? " and memberid='$memberid'" : '';
        $data = DB::query(1,$sql)->execute()->as_array();
        return $data[0];
    }

}