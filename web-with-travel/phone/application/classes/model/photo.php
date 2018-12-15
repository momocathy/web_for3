<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Class Model_Photo
 * 图片模块
 */
class Model_Photo extends ORM
{
    /**
     * 图片详情
     * @param $aid
     * @return mixed
     */
    public static function photo_detail($aid)
    {
        $sql = 'SELECT * FROM sline_photo ';
        $sql .= "WHERE webid={$GLOBALS['sys_webid']} AND ishidden=0 AND aid={$aid} ";
        $sql .= "limit 1";
        $arr = DB::query(1, $sql)->execute()->as_array();
        return $arr[0];
    }

    /**
     * 图片列表
     * @param $id
     * @return mixed
     */
    public static function photo_picture($id)
    {
        $sql = 'SELECT b.* FROM sline_photo AS a LEFT JOIN sline_photo_picture AS b ON a.id=b.pid ';
        $sql .= "WHERE a.id={$id} ";
        $sql .= 'ORDER BY IFNULL(b.displayorder,9999) ASC,b.id DESC';
        $arr = DB::query(1, $sql)->execute()->as_array();
        return $arr;
    }

    /**
     * 相册列表
     * @param $data
     * @return mixed
     */
    public static function photo_list($data)
    {
        $offset = $order = 0;
        $where = '';
        $filed = "a.aid,a.title,a.litpic,a.webid,a.description,a.shownum";
        extract($data);
        switch ($order)
        {
            //最新
            case '0':
                //$order = 'a.id DESC';
                $order = 'IFNULL(b.displayorder,9999) ASC';
                break;
            //点击量
            case '1':
                $order = 'a.shownum DESC,a.id DESC';
                break;
            //推荐
            case '2':
                $order = 'IFNULL(b.displayorder,9999) ASC,b.id DESC';
                break;
        }
        if (isset($kindlist))
        {
            $where .= " AND FIND_IN_SET({$kindlist},a.kindlist)";
        }
        if (isset($attrid))
        {
            foreach ($attrid as $v)
            {
                $where .= " AND FIND_IN_SET({$v},a.attrid)";
            }
        }
        $sql = "SELECT {$filed} FROM sline_photo AS a LEFT JOIN sline_allorderlist AS b ON (a.id=b.aid and b.typeid=6) ";
        $sql .= "WHERE a.webid=0 AND a.ishidden=0 {$where} ";
        $sql .= "ORDER BY {$order} ";
        $sql .= "LIMIT {$offset},10";
        $arr = DB::query(1, $sql)->execute()->as_array();
        return $arr;
    }

    /*
     * 获取目的地优化标题
     * */
    public static function search_seo($destpy)
    {
        if (!empty($destpy) && $destpy != 'all')
        {
            $destId = ORM::factory('destinations')->where("pinyin='$destpy' AND isopen=1")->find()->get('id');
            $info = ORM::factory('destinations', $destId)->as_array();
        }
        if (empty($info['id']))
        {
            $info = Model_Nav::get_channel_info(6);
            $seotitle = $info['seotitle'] ? $info['seotitle'] : $info['m_title'];
        }
        else
        {
            $seotitle = $info['seotitle'] ? $info['seotitle'] : $info['kindname'];
        }
        return array('seotitle' => $seotitle);
    }
}