<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Class Model_Visa 签证模块
 */
class Model_Visa extends ORM
{
    /**
     * 签证
     * @param $data
     * @return mixed
     */
    public static function parse_url($data)
    {
        $filed = 'a.id,a.aid,a.title,a.handleday,a.price,a.litpic,a.cityid';
        if ($data['area'] != 'all' && $data['area'])
        {
            $area = is_int($data['area']) ? "And b.id={$data['area']}" : "And b.pinyin='{$data['area']}'";
        }
        $where = "a.webid={$GLOBALS['sys_webid']} AND a.ishidden=0 {$area}";
        if (!empty($data['cityid']))
        {
            $where .= " And a.cityid={$data['cityid']}";
        }
        $offset = ($data['page'] - 1) * 10;
        $sql = "SELECT {$filed} FROM sline_visa AS a LEFT JOIN sline_visa_area AS b ON a.nationid=b.id ";
        $sql .= "WHERE {$where} ";
        $sql .= "ORDER BY a.id DESC ";
        $sql .= "LIMIT {$offset},10";
        $arr = DB::query(1, $sql)->execute()->as_array();
        return $arr;
    }

    /**
     * 签发目的地
     * @param $pinyin
     * @param string $filed
     * @return mixed
     */
    public static function vias_area($pinyin, $filed = 'pinyin')
    {
        $sql = "SELECT * FROM sline_visa_area WHERE webid={$GLOBALS['sys_webid']} AND isopen=1 AND {$filed}='{$pinyin}' LIMIT 1";
        $arr = DB::query(1, $sql)->execute()->as_array();
        return $arr[0];
    }

    /**
     * 签发城市
     * @return mixed
     */
    public static function visa_city()
    {
        $sql = "SELECT * FROM sline_visa_city where isopen=1";
        $arr = DB::query(1, $sql)->execute()->as_array();
        return $arr;
    }

    /**
     * 根据id获取签发城市
     * @param $cityid
     */
    public static function visa_city_by_id($cityid)
    {
        if (empty($cityid))
        {
            return;
        }
        $sql = "SELECT kindname FROM sline_visa_city WHERE isopen=1 AND id={$cityid}";
        $arr = DB::query(1, $sql)->execute()->as_array();
        return $arr[0];
    }

    /**
     * 签证详情 BY aid
     * @param $aid
     * @return mixed
     */
    public static function visa_detail($aid)
    {
        $sql = "SELECT * FROM sline_visa WHERE webid={$GLOBALS['sys_webid']} AND ishidden=0 AND aid={$aid}";
        $arr = DB::query(1, $sql)->execute()->as_array();
        return $arr[0];
    }

    /**
     * 签证详情 BY id
     * @param $id
     * @return mixed
     */
    public static function visa_detail_id($id)
    {
        $sql = "SELECT * FROM sline_visa WHERE webid={$GLOBALS['sys_webid']} AND ishidden=0 AND id={$id}";
        $arr = DB::query(1, $sql)->execute()->as_array();
        return $arr[0];
    }

    /**
     * 签证扩展字段
     * @param $id
     * @return mixed
     */
    public static function visa_extend($id)
    {
        $sql = "SELECT * FROM sline_visa_extend_field WHERE productid={$id}";
        $arr = DB::query(1, $sql)->execute()->as_array();
        return $arr;
    }

    /**
     * 搜索页标题
     * @param $destpy
     * @return array
     */
    public static function search_seo($destpy)
    {
        $data=array();
        if (!empty($destpy) && $destpy != 'all')
        {
            $info = DB::select()->from('visa_area')->where("pinyin='{$destpy}' AND isopen=1")->execute()->current();
            $seotitle = $info['seotitle'] ? $info['seotitle'] : $info['kindname'];
        }
        if (isset($seotitle) && !empty($seotitle))
        {
            $data[]=$seotitle;
        }
        $info = Model_Nav::get_channel_info(8);
        $data[] = $info['seotitle'] ? $info['seotitle'] : $info['m_title'];
        return array('seotitle' => implode('_',$data));
    }
}