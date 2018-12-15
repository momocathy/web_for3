<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Startplace extends ORM
{

    public static function start_city($cityId)
    {
        if (empty($cityId)||!is_int($cityId))
        {
            return;
        }
        $city = DB::select('cityname')->from('startplace')->where("id ={$cityId}")->execute()->current();
        return $city['cityname'];
    }
}