<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Taglib_Jieban
 * 结伴标签
 *
 */
class Taglib_Jieban
{
    private static $basefiled = 'a.id,a.kindlist,a.day,a.attrid,a.title,a.memo,a.shownum,a.startdate';

    public static function query($param)
    {

        $default = array(
            'row' => '10',
            'flag' => '',
            'offset' => 0,
            'destid' => 0
        );
        $param = array_merge($default, $param);
        extract($param);
        switch ($flag)
        {
            case 'new':
                $list = self::get_jieban_new($offset, $row);
                break;
            case 'order':
                $list = self::get_jieban_order($offset, $row);
                break;
        }
        foreach ($list as &$v)
        {
            //提取结伴图片
            $v['litpic'] = Model_Jieban::get_pic($v['memo']);
            $v['litpic'] = Common::img($v['litpic']);
            //活动类型
            $v['attrlist'] = Model_Jieban_Attr::get_attr_list($v['attrid']);
            //参与人
            $joinnumArr = Model_Jieban_Join::get_join_number($v['id']);
            $v['joinnum'] = intval($joinnumArr['adultnum'])+ intval($joinnumArr['childnum']);
            //描述
            $v['description'] = Common::cutstr_html($v['memo'],120);
            //URL地址
            $v['url'] = Common::get_web_url(0) . "/jieban/show_{$v['id']}.html";

        }
        return $list;

    }

    /**
     * 结伴最新
     * @param $offset
     * @param $row
     * @return mixed
     */
    private static function get_jieban_new($offset, $row)
    {
        $sql = "SELECT " . self::$basefiled . " FROM `sline_jieban` AS a ";
        $sql .= "WHERE a.ishidden=0 AND a.status=1 AND a.day>0 ";
        $sql .= "ORDER BY a.addtime DESC ";
        $sql .= "LIMIT {$offset},{$row}";
        $arr = self::execute($sql);
        return $arr;
    }

    /**
     * 结伴排序
     * @param $offset
     * @param $row
     * @return mixed
     */
    private static function get_jieban_order($offset, $row)
    {
        $sql = "SELECT " . self::$basefiled . " FROM `sline_jieban` AS a LEFT JOIN `sline_allorderlist` b ON (a.id=b.aid and b.typeid=11) ";
        $sql .= "WHERE a.ishidden=0 AND a.status=1 AND a.day>0 ";
        $sql .= "ORDER BY IFNULL(b.displayorder,9999) ASC,a.addtime DESC ";
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