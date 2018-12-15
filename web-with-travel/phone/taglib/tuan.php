<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Taglib_line
 * 线路标签
 *
 */
class  Taglib_Tuan
{
    private static $default = array(
        'row' => '10',
        'flag' => '',
        'offset' => 0,
        'destid' => 0
    );

    public static function query($param)
    {
        $filed = 'a.*';
        $param = array_merge(self::$default, $param);
        extract($param);
        switch ($flag)
        {
            case 'new':
                $list = Model_Tuan::tuan_list( $filed, $offset, $row);
        }
        foreach ($list as &$v)
        {
            $v['url'] = Common::get_web_url($v['webid']) . "/tuan/show_{$v['aid']}.html";
            $v['discount'] = $v['price'] == $v['sellprice'] ? '不打' : round($v['price'] / $v['sellprice'], 2) * 10;
            $v['sellnum'] = Model_Member_Order::get_sell_num($v['id'], 13) + intval($v['bookcount']);
        }
        return $list;
    }

    /**
     * 执行sql
     * @param $sql
     * @return mixed
     */
    private static function execute($sql)
    {
        $arr = DB::query(1, $sql)->execute()->as_array();
        return $arr;
    }
}