<?php defined('SYSPATH') or die('No direct access allowed.');


class Model_Line_Suit_Price extends ORM
{
    /**
     * @param $suitid
     * @param $useday
     * @return mixed
     * 产品套餐按天获取报价
     */

    public static function get_price_byday($suitid,$useday)
    {
        $sql = "SELECT * FROM `sline_line_suit_price` WHERE suitid='$suitid' AND day>='$useday' AND number!=0 limit 1";
        $ar = DB::query(1,$sql)->execute()->as_array();
        return $ar;
    }


}