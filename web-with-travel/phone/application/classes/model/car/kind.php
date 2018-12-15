<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Car_Kind extends ORM
{

    /*
        * 获取酒店
        * @param 参数
        * @return array
      */
    public static function get_carkindname($kindid)
    {
        $model=ORM::factory('car_kind',$kindid);
        if($model->loaded())
            return $model->get('kindname');
        return null;
    }

}