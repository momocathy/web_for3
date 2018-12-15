<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/26 0026
 * Time: 16:34
 */

class Taglib_Car {
    private static $typeid=3;
    private static $basefield ='a.id,
                                a.webid,
                                a.sellpoint,
                                a.aid,
                                a.kindlist,
                                a.title,
                                a.litpic,
                                a.shownum,
                                a.price,
                                a.satisfyscore,
                                a.bookcount,
                                a.attrid,
                                a.iconlist,
                                a.carkindid';
    /**
     * @description  租车类型
     */
    public static function kind($params)
    {
        $list=ORM::factory('car_kind')->get_all();
        return $list;
    }

    /**
     * @description 租车列表
     */
    public static function query($params)
    {
        $default=array(
            'row'=>'4',
            'offset'=>0,
            'flag'=>'new',
            'kindid'=>0

        );
        $params=array_merge($default,$params);
        extract($params);

        $queryFuncName='query_'.$flag;
        $list=null;
        switch($flag)
        {
            case 'new':
                 $list=self::query_new($params);
                break;
            case 'recommend':
                $list=self::query_recommend($params);
                break;
            case 'order':
                $list=self::query_recommend($params);
                break;

        }
        foreach($list as $k=>&$v)
        {
            $v['litpic'] = Common::img($v['litpic']);
            $v['url'] = Common::get_web_url($v['webid']).'/cars/show_'.$v['aid'].'.html';
            //价格
            $v['price'] = Model_Car::get_car_suit_price($v['aid'], $v['webid'] , $v['id']);
            //车辆属性
            $v['attrlist']= Model_Car_attr::get_attr_list($v['attrid']);
            //车型
            $v['kindname'] = Model_Car_Kind::get_carkindname($v['carkindid']);
        }


        return $list;
    }


    /**
     * 获取租车套餐
     * @param $params
     * @return Array
     */

    public static function suit($params)
    {
        $default=array('row'=>'10','productid'=>0);
        $params=array_merge($default,$params);
        extract($params);
        $suit = ORM::factory('car_suit')
            ->where("carid=:productid")
            ->param(':productid',$productid)
            ->get_all();
        foreach($suit as &$r)
        {
            $r['title'] = $r['suitname'];
        }
        return $suit;

    }

    /**
     * @desc 最新排序
     * @param $params
     * @return mixed
     */
    public static function query_new($params)
    {
        extract($params);
        $w='where a.id IS NOT NULL';
        $orderBy="ORDER BY a.modtime DESC";
        $values=array();

        //属性参数
        if(!empty($attrid))
        {
            $values[':attrid']=$attrid;
            $w.=" and FIND_IN_SET(:attrid,a.attrid)";
        }
        //判断目的地
        if(empty($destid))
        {
            $sql = "SELECT ".self::$basefield." FROM sline_car a LEFT JOIN sline_allorderlist b";
            $sql.=" ON(a.id=b.aid AND b.typeid=3) {$w} {$orderBy} limit {$offset},{$row}";
        }
        else
        {
            $w.=" AND FIND_IN_SET(:destid,kindlist)";
            $values[':destid']=$destid;
            $sql = "SELECT ".self::$basefield." FROM sline_car a LEFT JOIN sline_kindorderlist b";
            $sql.=" ON(a.id=b.aid AND b.typeid=3 AND b.classid=:destid) {$w} {$orderBy}  limit {$offset},{$row}";
        }

        $list=DB::query(Database::SELECT,$sql)->parameters($values)->execute()->as_array();
        return $list;
    }

    /**
     * @desc 按displayorder排序
     * @param $params
     */
    public static function query_recommend($params)
    {
        extract($params);
        $w='where a.id!=0';
        $orderBy="ORDER BY b.displayorder DESC";
        $values=array();

        //属性参数
        if(!empty($attrid))
        {
            $values[':attrid']=$attrid;
            $w.=" and FIND_IN_SET(:attrid,a.attrid)";
        }

        if(!empty($carkindid))
        {
            $values[':carkindid']=$carkindid;
            $w.=" and carkindid=:carkindid";
        }
        //判断目的地
        if(empty($destid))
        {
            $sql = "SELECT ".self::$basefield." FROM sline_car a LEFT JOIN sline_allorderlist b";
            $sql.=" ON(a.id=b.aid AND b.typeid=3) {$w} {$orderBy}  limit {$offset},{$row}";
        }
        else
        {
            $w.=" AND FIND_IN_SET(:destid,kindlist)";
            $values[':destid']=$destid;
            $sql = "SELECT ".self::$basefield." FROM sline_car a LEFT JOIN sline_kindorderlist b";
            $sql.=" ON(a.id=b.aid AND b.typeid=3 AND b.classid=:destid) {$w} {$orderBy}  limit {$offset},{$row}";
        }
        $list=DB::query(Database::SELECT,$sql)->parameters($values)->execute()->as_array();
        return $list;
    }

    /**
     * 获取租车类型
     * @param $params
     * @return array
     */
    public static function kind_list($params)
    {
        $default=array('row'=>'10');
        $params=array_merge($default,$params);
        extract($params);
        $arr = ORM::factory('car_kind')
            ->where("webid=0")
            ->limit($row)
            ->get_all();
        foreach($arr as &$row)
        {
            $row['title'] = $row['kindname'];
        }
        return $arr;
    }

    /**
     * 获取车辆属性
     * @param $params
     * @return array
     */
    public static function attr_list($params)
    {
        $default=array('row'=>'10');
        $params=array_merge($default,$params);
        extract($params);
        $arr = ORM::factory('car_attr')
            ->where("webid=0 AND isopen=1 AND pid>0")
            ->limit($row)
            ->get_all();
        foreach($arr as &$v)
        {
            $v['title'] = $v['attrname'];
        }
        return $arr;
    }
}