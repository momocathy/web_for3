<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Spot extends ORM
{



    public static $typeid = 5;
    /**
     * 参数解析
     * @param $params
     */
    public static function search_result($params,$keyword,$pagesize='10')
    {
        $destPy=$priceId=$sortType=$attrId=0;
        $page = 1;
        $params = explode('-', str_replace('/', '-', $params));
        $count = count($params);
        switch ($count)
        {
            //目的地
            case 1:
                list($destPy) = $params;
                break;
            //关键字、属性
            case 6:
                list($destPy, $priceId, $sortType, $k, $attrId,$page) = $params;
                break;
        }
        $destPy = Common::remove_xss($destPy);
        $priceId = Common::remove_xss($priceId);
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

        //价格区间
        if($priceId)
        {
            $priceArr = ORM::factory('spot_pricelist',$priceId)->as_array();
            $min = $priceArr['min'] ? $priceArr['min'] : 0 ;
            $where.= " AND a.price BETWEEN {$min} AND {$priceArr['max']} ";
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
            $sql = "SELECT a.* FROM `sline_spot` a ";
            $sql.= "LEFT JOIN `sline_kindorderlist` b ";
            $sql.= "ON (a.id=b.aid AND b.typeid=5 AND a.webid=b.webid AND b.classid=$destId)";
            $sql.= $where;
            $sql.= "ORDER BY {$orderBy}a.modtime DESC,a.addtime DESC ";
            $sql.= "LIMIT {$offset},{$pagesize}";

        }
        else
        {
            $sql = "SELECT a.* FROM `sline_spot` a ";
            $sql.= "LEFT JOIN `sline_allorderlist` b ";
            $sql.= "ON (a.id=b.aid AND b.typeid=5 AND a.webid=b.webid)";
            $sql.= $where;
            $sql.= "ORDER BY {$orderBy}a.modtime DESC,a.addtime DESC ";
            $sql.= "LIMIT {$offset},{$pagesize}";


        }
        $data = DB::query(1,$sql)->execute()->as_array();
        foreach($data as &$v)
        {
            $priceArr = Model_Spot::get_minprice($v['id']);
            $v['commentnum'] = Model_Comment::get_comment_num($v['id'],self::$typeid); //评论次数
            $v['sellnum'] = Model_Member_Order::get_sell_num($v['id'],self::$typeid)+intval($v['bookcount']); //销售数量
            $v['sellprice'] = $priceArr['sellprice'];//挂牌价
            $v['price'] = $priceArr['price'];//最低价
            $v['attrlist'] = Model_Hotel_Attr::get_attr_list($v['attrid']);//属性列表.
            $v['url'] = Common::get_web_url($v['webid']) . "/spots/show_{$v['aid']}.html";
            $v['litpic'] = Common::img($v['litpic']);

        }
        return Product::list_search_format($data, $page);
    }
    /**
     * @param $hotelid
     * @return mixed
     * 获取景点最低价
     */
    public static function get_minprice($spotid)
    {

        $sql = "SELECT MIN(ourprice) AS price,MIN(sellprice) AS sellprice FROM `sline_spot_ticket` ";
        $sql.= "WHERE spotid='$spotid' AND ourprice!=0";
        $row = DB::query(1,$sql)->execute()->as_array();
        return $row[0];
    }

    /**
     * @param $spotid
     * @return array
     * 获取扩展字段信息
     */
    public static function get_extend_info($spotid)
    {
        $row = ORM::factory('spot_extend_field')
            ->where("productid=".$spotid)
            ->find()
            ->as_array();
        return $row;
    }

    /**
     * @param $spotid
     * @return bool
     * 检测是否有门票
     */
    public static function has_ticket($spotid)
    {
        $arr = ORM::factory('spot_ticket')->where("spotid='$spotid'")->get_all();
        return count($arr) ? true : false;
    }


}