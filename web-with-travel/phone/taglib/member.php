<?php
/**
 * Created by PhpStorm.
 * Author: netman
 * QQ: 87482723
 * Time: 15-9-28 下午7:35
 * Desc:会员标签
 */
class Taglib_Member {

    /*
     * 获取常用联系人
     * @param 参数
     * @return array

   */
    public static function linkman($params)
    {
        $default=array('memberid'=>'');
        $params=array_merge($default,$params);
        extract($params);
        $sql="SELECT * FROM `sline_member_linkman` WHERE memberid=:memberid";
        $ar = DB::query(1,$sql)->param(':memberid',$memberid)->execute()->as_array();
        return $ar;
    }

}