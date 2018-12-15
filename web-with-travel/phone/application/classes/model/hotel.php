<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Hotel extends ORM
{

    //取得酒店的挂牌价格
    public static function get_sellprice($hotelid)
    {
        $sql="SELECT MIN(price) AS minprice,sellprice FROM `sline_hotel_room` WHERE hotelid='{$hotelid}'";
        $row = DB::query(1,$sql)->execute()->as_array();
        return intval($row[0]['sellprice']);
    }
    /*
    * 获取酒店最低价
    * */
    public static function get_minprice($hotelid)
    {

        $sql = "SELECT MIN(price) AS price FROM `sline_hotel_room_price` WHERE hotelid='$hotelid' AND price!=0 AND day>=UNIX_TIMESTAMP()";
        $row = DB::query(1,$sql)->execute()->as_array();
        return $row[0]['price'];
    }

    /**
     * 参数解析
     * @param $params
     */
    public static function search_result($params,$keyword,$pagesize='10')
    {
        $destId=$destPy=$rankId=$priceId=$sortType=$keyword=$attrId=0;
        $page = 1;
        $params = explode('-', str_replace('/', '-', $params));
        $count = count($params);
        switch ($count)
        {
            //目的地
            case 1:
                list($destpy) = $params;
                break;
            //关键字、属性
            case 7:
                list($destPy, $rankId, $priceId, $sortType, $keyword, $attrId,$page) = $params;
                break;
        }
        $destPy = Common::remove_xss($destPy);
        $rankId = Common::remove_xss($rankId);
        $sortType = Common::remove_xss($sortType);
        $keyword = Common::remove_xss($keyword);
        $attrId = Common::remove_xss($attrId);
        $page = Common::remove_xss($page);

        $where = ' WHERE a.ishidden=0 ';
        //按目的地搜索
        if($destPy && $destPy!='all')
        {
            $destId = ORM::factory('destinations')->where("pinyin='$destPy'")->find()->get('id');
            $where.= " AND FIND_IN_SET('$destId',a.kindlist) ";
        }
        //星级
        if($rankId)
        {
            $where.= " AND a.hotelrankid='$rankId'";
        }
        //价格区间
        if($priceId)
        {
            $priceArr = ORM::factory('hotel_pricelist',$priceId)->as_array();
            $where.= " AND a.price BETWEEN {$priceArr['min']} AND {$priceArr['max']} ";
        }
        //排序
        $orderBy = "";
        if(!empty($sortType))
        {
            if($sortType==0)//默认排序
            {
                $orderBy = " IFNULL(b.displayorder,9999) ASC,";
            }
            else if($sortType==1)//特价排序
            {
                $orderBy = "  a.price asc,";
            }
            else if($sortType==2) //价格
            {
                $orderBy = "  a.price desc,";
            }
            else if($sortType==3) //销量
            {
                $orderBy = " a.bookcount desc,";
            }
            else if($sortType==4)//人气
            {
                $orderBy = " a.shownum desc,";
            }
            else if($sortType==5) //满意度
            {
                $orderBy = " a.shownum desc,";
            }
        }

        //关键词
        if(!empty($keyword))
        {
            $where.= " AND a.title like '%$keyword%' ";
        }
        //按属性
        if(!empty($attrId))
        {
            $where.= Product::get_attr_where($attrId);
        }

        $offset = (intval($page)-1)*$pagesize;

        //如果选择了目的地
        if(!empty($destId))
        {
            $sql = "SELECT a.* FROM `sline_hotel` a ";
            $sql.= "LEFT JOIN `sline_kindorderlist` b ";
            $sql.= "ON (a.id=b.aid AND b.typeid=2 AND a.webid=b.webid AND b.classid=$destId)";
            $sql.= $where;
            $sql.= "ORDER BY {$orderBy}a.modtime DESC,a.addtime DESC ";
            $sql.= "LIMIT {$offset},{$pagesize}";

        }
        else
        {
            $sql = "SELECT a.* FROM `sline_hotel` a ";
            $sql.= "LEFT JOIN `sline_allorderlist` b ";
            $sql.= "ON (a.id=b.aid AND b.typeid=2 AND a.webid=b.webid)";
            $sql.= $where;
            $sql.= "ORDER BY {$orderBy}a.modtime DESC,a.addtime DESC ";
            $sql.= "LIMIT {$offset},{$pagesize}";


        }
        $data = DB::query(1,$sql)->execute()->as_array();
        foreach($data as &$v)
        {
            $v['commentnum'] = Model_Comment::get_comment_num($v['id'],2); //评论次数
            $v['satisfyscore'] = Model_Comment::get_score($v['id'], 2, $v['satisfyscore'], $v['commentnum']);//满意度
            $v['sellnum'] = Model_Member_Order::get_sell_num($v['id'],2)+intval($v['bookcount']); //销售数量
            $v['sellprice'] = Model_Hotel::get_sellprice($v['id']);//挂牌价
            $v['price'] = Model_Hotel::get_minprice($v['id']);//最低价
            $v['attrlist'] = Model_Hotel_Attr::get_attr_list($v['attrid']);//属性列表.
            $v['url'] = Common::get_web_url($v['webid']) . "/hotels/show_{$v['aid']}.html";
            $v['litpic'] = Common::img($v['litpic']);
            $v['hotelrankid']=ORM::factory('hotel_rank',$v['hotelrankid'])->get('hotelrank');
        }
        return Product::list_search_format($data, $page);
    }
    /*
     * 获取目的地优化标题
     * */
    public static function search_seo($destpy)
    {
       if(!empty($destpy) && $destpy!='all')
       {
           $destId = ORM::factory('destinations')->where("pinyin='$destpy' AND isopen=1")->find()->get('id');
           $info = ORM::factory('destinations',$destId)->as_array();
           $seotitle = $info['seotitle'] ? $info['seotitle'] : $info['kindname'];
       }
       else
       {
           $info = Model_Nav::get_channel_info(2);
           $seotitle = $info['seotitle'] ? $info['seotitle'] : $info['m_title'];
       }

       return array('seotitle'=>$seotitle);
    }

    /*
     *获取套餐日期范围内价格总和.
     * */
    public static function suit_range_price($suitid,$sdate,$edate,$dingnum)
    {
        $sdate = strtotime($sdate);
        $edate = strtotime($edate);
        $sql = "SELECT price FROM`sline_hotel_room_price` ";
        $sql.= "WHERE suitid='$suitid' AND day>=$sdate AND day<$edate";
        $ar = DB::query(1,$sql)->execute()->as_array();
        $price = 0;
        foreach($ar as $row)
        {
            $price+=$row['price']*$dingnum;
        }
        return $price;
    }



    /**
     * @param $suitid
     * @return mixed
     * 获取套餐最小可预订日期
     */

    public static function suit_mindate_book($suitid)
    {
        $time = time();
        $sql = "SELECT day FROM`sline_hotel_room_price` ";
        $sql.= "WHERE suitid='$suitid' AND day>=$time AND price>0 AND number!=0 LIMIT 1";
        $ar = DB::query(1,$sql)->execute()->as_array();
        return $ar[0]['day'];
    }


    /**
     * @param $suitid
     * @param $sdate
     * @param $edate
     * @param $dingnum
     * @return bool
     *
     */
    public static function check_suit_storage($suitid,$sdate,$edate,$dingnum)
    {
        $sdate = strtotime($sdate);
        $edate = strtotime($edate);
        $sql = "SELECT number FROM`sline_hotel_room_price` ";
        $sql.= "WHERE suitid='$suitid' AND day>=$sdate AND day<$edate";
        $ar = DB::query(1,$sql)->execute()->as_array();
        $flag = 1;
        foreach($ar as $row)
        {
           if($row['number']!='-1' && $row['number']<$dingnum)
           {
               $flag = 0;
               break;
           }
        }
        return $flag;
    }
}