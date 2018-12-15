<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Taglib_line
 * 线路标签
 *
 */
class Taglib_line
{
    private static $basefiled = 'a.id,a.aid,a.title,a.storeprice,a.litpic,a.startcity,a.kindlist,a.attrid';

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
                $list = self::get_line_new($offset, $row);
                break;
            case 'order':
                $list = self::get_line_order($offset, $row);
                break;
            case 'mdd':
                $list = self::get_line_bymdd($offset, $row, $destid);
                break;
        }
        foreach ($list as &$v)
        {

            $v['price'] = Model_Line::get_minprice($v['id']);

            $v['attrlist'] = Model_Line::line_attr($v['attrid']);

            $v['url'] = Common::get_web_url($v['webid']) . "/lines/show_{$v['aid']}.html";

            $v['startcity'] = Model_Startplace::start_city($v['startcity']);

            $v['iconlist'] = Product::get_ico_list($v['icolist']);
        }

        return $list;

    }

    /**
     * 获取线路套餐列表
     * @param $params
     * @return Array
     */

    public static function suit($params)
    {
        $default=array('row'=>'10','productid'=>0);
        $params=array_merge($default,$params);
        extract($params);
        $suit = ORM::factory('line_suit')
            ->where("lineid=:productid")
            ->param(':productid',$productid)
            ->get_all();
        foreach($suit as &$r)
        {


            $r['title'] = $r['suitname'];
        }
        return $suit;

    }

    /**
     * @param $params
     * @return Array
     * 天数读取
     */

    public static function day_list($params)
    {
        $default=array('row'=>'10');
        $params=array_merge($default,$params);
        extract($params);
        $suit = ORM::factory('line_day')
            ->where("isdisplay=1")
            ->get_all();
        foreach($suit as &$r)
        {
            $r['title'] = $r['word'];
        }
        return $suit;
    }

    public static function price_list($params)
    {
        $default=array('row'=>'10');
        $params=array_merge($default,$params);
        extract($params);
        $suit = ORM::factory('line_pricelist')
            ->get_all();
        foreach($suit as &$row)
        {
            if($row['lowerprice']!=''&& $row['highprice']!='')
            {
                $row['title'] = '&yen;'.$row['lowerprice'].'元-'.'&yen;'.$row['highprice'].'元';
            }
            else if($row['lowerprice']=='')
            {
                $row['title']='&yen;'.$row['highprice'].'元以下';
            }
            else if($row['highprice']=='')
            {
                $row['title']='&yen;'.$row['lowerprice'].'元以上';
            }
        }
        return $suit;
    }

    /**
     * 线路最新
     * @param $offset
     * @param $row
     * @return mixed
     */
    private static function get_line_new($offset, $row)
    {
        $sql = "SELECT " . self::$basefiled . " FROM `sline_line` AS a ";
        $sql .= "WHERE a.hidden=0 AND a.webid=0 ";
        $sql .= "ORDER BY a.modtime DESC,a.addtime DESC ";
        $sql .= "LIMIT {$offset},{$row}";
        $arr = self::execute($sql);
        return $arr;
    }

    /**
     * 线路排序
     * @param $offset
     * @param $row
     * @return mixed
     */
    private static function get_line_order($offset, $row)
    {
        $sql = "SELECT " . self::$basefiled . " FROM `sline_line` AS a LEFT JOIN `sline_allorderlist` b ON (a.id=b.aid and b.typeid=1) ";
        $sql .= "WHERE a.ishidden=0 AND  a.webid=0 ";
        $sql .= "ORDER BY IFNULL(b.displayorder,9999) ASC,a.modtime DESC,a.addtime DESC ";
        $sql .= "LIMIT {$offset},{$row}";
        $arr = self::execute($sql);
        return $arr;
    }

    /**
     * 线路目的地
     * @param $offset
     * @param $row
     * @param $destid
     * @return mixed
     */
    private static function get_line_bymdd($offset, $row, $destid)
    {
        $sql = "SELECT " . self::$basefiled . " FROM `sline_line` AS a LEFT JOIN `sline_allorderlist` b ON (a.id=b.aid and b.typeid=1) ";
        $sql .= "WHERE a.ishidden=0 AND FIND_IN_SET('{$destid}',a.kindlist) AND a.webid=0 ";
        $sql .= "ORDER BY IFNULL(b.displayorder,9999) ASC,a.modtime DESC,a.addtime DESC ";
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