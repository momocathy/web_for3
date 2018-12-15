<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Jieban extends ORM
{

    /**
     * 结伴详情
     * @param $id
     * @return array
     */
    public static function detail($id)
    {
        $sql = "SELECT * FROM sline_jieban WHERE id={$id} AND ishidden=0";
        $data = DB::query(1, $sql)->execute()->as_array();
        return $data[0];
    }

    /**
     * 结伴目的地
     * @param string $kindlist
     * @return string
     */
    public static function get_kindnamelist($kindlist='',$separator='-')
    {
        if (empty($kindlist) || is_null($kindlist) || $kindlist == 'null')
        {
            return '';
        }

        $kindArr = explode(',',$kindlist);
        $kindStr = '';
        foreach($kindArr as $k=>$v)
        {
            $arr = DB::select('kindname')->from('destinations')->where("id=".$v)->execute()->as_array();
            if($arr[0]['kindname'])
            {
                $kindStr.= $arr[0]['kindname'].$separator;
            }
        }
        $kindStr = trim($kindStr, $separator);

        return $kindStr;
    }


    /**
     * 详情页中提取seo
     * @param $arr
     * @return array
     */
    public static function seo($arr)
    {
        $description = common::cutstr_html($arr['memo'],120);
        $description = $description ? $description : $arr['title'];
        $seoArr = array(
            'seotitle' => $arr['title'],
            'keyword' => $arr['title'],
            'description' => $description
        );
        foreach ($seoArr as &$v)
        {
            $v = trim($v);
        }
        return $seoArr;
    }

    /**
     * 根据ID读取参考推荐
     * @param string $kindlist
     * @param string $day
     * @param int $row
     * @return mixed
     */
    public static function get_line_recommend($kindlist = '', $day = '',$row = 3)
    {
        $sql = "SELECT a.id,a.title,a.litpic,a.price,a.webid,a.aid FROM `sline_line` AS a LEFT JOIN `sline_allorderlist` b ON (a.id=b.aid and b.typeid=1) ";
        $sql .= "WHERE a.ishidden=0 AND a.webid=0 AND kindlist='{$kindlist}' AND lineday='{$day}' ";
        $sql .= "ORDER BY IFNULL(b.displayorder,9999) ASC,a.addtime DESC ";
        $sql .= "LIMIT 0,".$row;
        $data = DB::query(1,$sql)->execute()->as_array();
        return $data;
    }

    /**
     * 结伴排序
     * @param $offset
     * @param $row
     * @return mixed
     */
    public static function get_jieban_list($page, $pagesize)
    {
        $offset = ($page-1)*$pagesize;
        $sql = "SELECT a.id,a.kindlist,a.day,a.attrid,a.title,a.memo,a.shownum FROM `sline_jieban` AS a LEFT JOIN `sline_allorderlist` b ON (a.id=b.aid and b.typeid=11) ";
        $sql .= "WHERE a.ishidden=0 AND a.status=1 AND a.day>0 ";
        $sql .= "ORDER BY IFNULL(b.displayorder,9999) ASC,a.addtime DESC ";
        $sql .= "LIMIT {$offset},{$pagesize}";
        $data = DB::query(1,$sql)->execute()->as_array();

        foreach ($data as &$v)
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
            $v['description'] = common::cutstr_html($v['memo'],120);
            //URL地址
            $v['url'] = Common::get_web_url(0) . "/jieban/show_{$v['id']}.html";

        }
        return Product::list_search_format($data, $page);
    }
    /**
     * 回去结伴内容中的第一个图片
     * @param $str
     * @return mixed
     */
    public static function get_pic($str){
        preg_match_all("/<img.*\>/isU",$str,$ereg);//正则表达式把图片的整个都获取出来了
        $img=$ereg[0][0];//图片
        preg_match("#src=('|\")(.*)('|\")#isU",$img,$match);
        $img_path =$match[2];//获取第一张图片路径
        return $img_path;
    }

    /**
     * 判断用户是否已加入结伴
     * @param $memberid   会员ID
     * @param $jiebanid   结伴ID
     * @return mixed
     */
    public static function repeat_join($memberid, $jiebanid )
    {
        $where = "jiebanid={$jiebanid} AND memberid={$memberid}";
        $result = DB::select()->from('jieban_join')->where($where)->execute()->as_array();
        if (!empty($result))
        {
            $result = $result[0];
        }
        return $result;
    }
}