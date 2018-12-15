<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Class Model_Tuan 团购模块
 */
class Model_Tuan extends ORM
{
    /**
     * 团购tag列表
     * @param $filed
     * @param $offset
     * @param $row
     * @return mixed
     */
    public static function tuan_list($filed, $offset, $row)
    {
        $sql = "SELECT {$filed} FROM sline_tuan as a LEFT JOIN `sline_allorderlist` b ON (a.id=b.aid AND b.typeid=13 AND a.webid=b.webid) ";
        $sql .= 'WHERE endtime >unix_timestamp(now()) AND a.webid=0 AND ishidden=0 ';
        $sql .= 'ORDER BY IFNULL(b.displayorder,9999) ASC,a.modtime DESC,a.addtime DESC ';
        $sql .= "LIMIT {$offset},{$row}";
        $arr = DB::query(1, $sql)->execute()->as_array();
        return $arr;
    }

    /**
     * 团购能个列表
     * @param $data
     */
    public static function list_data($data)
    {
        //查询目的地ID
        $where = 'a.ishidden=0 and a.endtime >unix_timestamp(now()) ';
        if ($data['destPy'] != 'all')
        {
            $destId = ORM::factory('destinations')->where("pinyin='{$data['destPy']}'")->find()->get('id');
            $where .= "AND FIND_IN_SET('$destId',a.kindlist) ";
        }
        //按状态 0：进行中 1：未开始
        if ($data['status'])
        {
            $where .= 'And ' . ($data['status'] == 1 ? 'a.starttime <' . time() : 'a.starttime >' . time()) . ' ';
        }
        //按属性
        if (!empty($data['attrId']))
        {
            $where .= Product::get_attr_where($data['attrId']);
        }
        //分页
        if (!isset($data['page']))
        {
            $data['page'] = 1;
        }
        //偏移量
        $offset = ($data['page'] - 1) * 10;
        //排序
        switch ($data['sorttype'])
        {
            case 1:
                $order = 'a.price asc,';
                break;
            case 2:
                $order = 'a.price desc,';
                break;
            case 3:
                $order = 'a.shownum desc,';
                break;
            case 4:
                $order = 'a.bookcount desc,';
                break;
        }
        //关键字
        if (!empty($data['keyword']))
        {
            $where .= " AND a.title like '%{$data['keyword']}%'";
        }
        //组合sql
        $sql = "SELECT a.* FROM `sline_tuan` a ";
        $sql .= "LEFT JOIN `sline_allorderlist` b ";
        if (empty($destId))
        {
            $sql .= 'ON (a.id=b.aid AND b.typeid=13 AND a.webid=b.webid) ';
        }
        else
        {
            $sql .= "ON (a.id=b.aid AND b.typeid=13 AND a.webid=b.webid AND b.classid={$destId}) ";
        }
        $sql .= 'WHERE ' . $where;
        $sql .= "ORDER BY {$order}IFNULL(b.displayorder,9999) ASC,a.modtime DESC,a.addtime DESC ";
        $sql .= "LIMIT {$offset},10";
        $arr = DB::query(1, $sql)->execute()->as_array();
        return $arr;
    }

    /**
     * 团购详情 BY aid
     * @param $aid
     * @return mixed
     */
    public static function tuan_detail($aid)
    {
        $sql = "SELECT * FROM sline_tuan ";
        $sql .= "WHERE webid={$GLOBALS['sys_webid']} AND ishidden=0 AND aid={$aid} ";
        $sql .= "limit 1";
        $arr = DB::query(1, $sql)->execute()->as_array();
        return $arr[0];
    }

    /**
     * 团购详情 BY id
     * @param $id
     * @return mixed
     */
    public static function tuan_detail_id($id)
    {
        $sql = "SELECT * FROM sline_tuan ";
        $sql .= "WHERE webid={$GLOBALS['sys_webid']} AND ishidden=0 AND id={$id} ";
        $sql .= "limit 1";
        $arr = DB::query(1, $sql)->execute()->as_array();
        return $arr[0];
    }

    /**
     * 团购扩展字段
     * @param $id
     * @return mixed
     */
    public static function tuan_extend($id)
    {
        $sql = "SELECT * FROM sline_tuan_extend_field ";
        $sql .= "WHERE productid={$id}";
        $arr = DB::query(1, $sql)->execute()->as_array();
        return $arr;
    }

    /**
     * 获取目的地优化标题
     * @param $destpy
     * @return array
     */
    public static function search_seo($destpy)
    {
        if (!empty($destpy) && $destpy != 'all')
        {
            $destId = ORM::factory('destinations')->where("pinyin='$destpy' AND isopen=1")->find()->get('id');
            $info = ORM::factory('destinations', $destId)->as_array();
            $seotitle = $info['seotitle'] ? $info['seotitle'] : $info['kindname'];
        }
        else
        {
            $info = Model_Nav::get_channel_info(13);
            $seotitle = $info['seotitle'] ? $info['seotitle'] : $info['m_title'];
        }
        return array('seotitle' => $seotitle);
    }
}