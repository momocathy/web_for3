<?php
defined('SYSPATH') or die('No direct access allowed.');
/**
 * Created by Phpstorm.
 * User: netman
 * Date: 15-9-23
 * Time: 上午10:43
 * Desc: 酒店标签
 */

class Taglib_Hotel {

    /*
     * 获取酒店
     * @param 参数
     * @return array

   */
    private static $basefield ='a.id,
                                a.webid,
                                a.sellpoint,
                                a.aid,
                                a.kindlist,
                                a.title,
                                a.address,
                                a.litpic,
                                a.hotelrankid,
                                a.webid,
                                a.hotelrankid,
                                a.shownum,
                                a.price,
                                a.satisfyscore,
                                a.bookcount,
                                a.attrid,
                                a.iconlist';

    /**
     * @param $params
     * @return mixed
     * @description 标签接口
     */
    public static function query($params)
    {
        $default=array(
            'row'=>'10',
            'flag'=>'',
            'offset'=>0,
            'destid'=>0
        );
        $params=array_merge($default,$params);
        extract($params);
        switch($flag)
        {
            case 'new':
                $list = self::get_hotel_new($offset,$row);
                break;
            case 'order':
                $list = self::get_hotel_order($offset,$row);
                break;
            case 'mdd':
                $list = self::get_hotel_bymdd($offset,$row,$destid);
        }
        //对获取的数据进行初始化处理
        foreach($list as &$v)
        {
            $v['commentnum'] = Model_Comment::get_comment_num($v['id'],2); //评论次数
            $v['sellnum'] = Model_Member_Order::get_sell_num($v['id'],2)+intval($v['bookcount']); //销售数量
            $v['sellprice'] = Model_Hotel::get_sellprice($v['id']);//挂牌价
            $v['price'] = Model_Hotel::get_minprice($v['id']);//最低价
            $v['attrlist'] = Model_Hotel_Attr::get_attr_list($v['attrid']);//属性列表.
            $v['url'] = Common::get_web_url($v['webid']) . "/hotels/show_{$v['aid']}.html";
        }//var_dump($list);
        return $list;

    }

    /**
     * 获取酒店套餐
     * @param $params
     * @return Array
     */

    public static function suit($params)
    {
        $default=array('row'=>'10','productid'=>0);
        $params=array_merge($default,$params);
        extract($params);
        $suit = ORM::factory('hotel_room')
            ->where("hotelid=:productid")
            ->param(':productid',$productid)
            ->get_all();
        foreach($suit as &$r)
        {
            $r['title'] = $r['roomname'];
        }
        return $suit;

    }

    /**
     * 获取酒店报价列表
     * @param $params
     * @return array
     */
    public static function price_list($params)
    {
        $default=array('row'=>'10');
        $params=array_merge($default,$params);
        extract($params);
        $arr = ORM::factory('hotel_pricelist')
            ->where("webid=0")
            ->limit($row)
            ->get_all();
        foreach($arr as &$row)
        {
            if($row['min']!=''&& $row['max']!='')
            {
                $row['title'] = '&yen;'.$row['min'].'元-'.'&yen;'.$row['max'].'元';
            }
            else if($row['min']=='')
            {
                $row['title']='&yen;'.$row['max'].'元以下';
            }
            else if($row['max']=='')
            {
                $row['title']='&yen;'.$row['min'].'元以上';
            }
        }
        return $arr;

    }

    /**
     * 酒店星级列表
     * @param $params
     * @return array
     */
    public static function rank_list($params)
    {
        $default=array('row'=>'10');
        $params=array_merge($default,$params);
        extract($params);
        $arr = ORM::factory('hotel_rank')
            ->where("webid=0")
            ->order_by('id','ASC')
            ->limit($row)
            ->get_all();
        foreach($arr as &$row)
        {
            $row['title'] = $row['hotelrank'];
        }
        return $arr;

    }
    /*
     * 获取最新酒店
     * */
    private static function get_hotel_new($offset,$row)
    {
        $sql = "SELECT ".self::$basefield." FROM sline_hotel a ";
        $sql.= "WHERE a.ishidden=0 ORDER BY a.modtime desc,a.addtime DESC ";
        $sql.= "LIMIT {$offset},{$row}";
        $arr = self::execute($sql);
        return $arr;

    }
    /*
     * 按顺序获取酒店
     * */
    private static function get_hotel_order($offset,$row)
    {
        $sql = "SELECT ".self::$basefield." FROM sline_hotel a ";
        $sql.= "LEFT JOIN `sline_allorderlist` b ";
        $sql.= "ON (a.id=b.aid and b.typeid=2) ";
        $sql.= "WHERE a.ishidden=0 ";
        $sql.= "ORDER BY IFNULL(b.displayorder,9999) asc,a.modtime desc,a.addtime DESC ";
        $sql.= "limit {$offset},{$row}";
        $arr = self::execute($sql);
        return $arr;


    }
    /*
     * 按目的地获取酒店
     * */
    private static function get_hotel_bymdd($offset,$row,$destid)
    {
        $sql = "SELECT ".self::$basefield." FROM `sline_hotel` a ";
        $sql.= "LEFT JOIN `sline_kindorderlist` b ";
        $sql.= "ON (a.id=b.aid and b.typeid=2) ";
        $sql.= "WHERE a.ishidden=0 AND FIND_IN_SET($destid,a.kindlist) ";
        $sql.= "ORDER BY IFNULL(b.displayorder,9999) asc,a.modtime desc,a.addtime DESC ";
        $sql.= "LIMIT {$offset},{$row}";
        $arr = self::execute($sql);
        return $arr;
    }
    /*
     * 执行sql语句
     * */
    private static function execute($sql)
    {
        $arr = DB::query(1,$sql)->execute()->as_array();
        return $arr;
    }

} 