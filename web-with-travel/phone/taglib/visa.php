<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Taglib_Visa
 * 签证标签
 */
class Taglib_Visa
{
    //private static $basefiled = '';
    private static $default = array(
        'row' => '10',
        'flag' => '',
        'offset' => 0,
        'destid' => 0,
        'pid' => 0
    );

    /**
     * 签证
     * @param $param
     */
    public static function query($param)
    {

    }

    /**
     * 签证热门国家、地区
     * @param $param
     * @return mixed
     */
    public static function area($param)
    {
        $param = array_merge(self::$default, $param);
        extract($param);
        switch ($flag)
        {
            case 'order':
                $list = self::get_area_order($offset, $row);
                break;
            case 'query':
                $list = self::get_area_query($offset, $row, $pid);
                break;
        }
        foreach ($list as &$v)
        {
            $v['litpic'] = Common::img($v['litpic']);
            $v['url'] = Common::get_web_url($v['webid']) . "/visa/{$v['pinyin']}/";
        }
        return $list;
    }

    /**
     * @param $offset
     * @param $row
     * @return mixed
     */
    public static function get_area_order($offset, $row)
    {
        $sql = 'SELECT * FROM sline_visa_area ';
        $sql .= 'WHERE litpic is not null and isopen=1 and webid=0 ';
        $sql .= 'order by displayorder asc,id desc ';
        $sql .= "LIMIT {$offset},{$row}";
        $arr = self::execute($sql);
        return $arr;
    }

    public static function get_area_query($offset, $row, $pid)
    {
        $sql = 'SELECT * FROM sline_visa_area ';
        $sql .= "WHERE pid={$pid} and isopen=1 and webid=0 ";
        $sql .= 'order by displayorder asc,id desc ';
        $sql .= "LIMIT {$offset},{$row}";
        $arr = self::execute($sql);
        return $arr;
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