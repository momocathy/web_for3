<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Destinations extends ORM
{
   
    public static function  home_display()
    {
        $sql = 'select * from (select kindid,displayorder from sline_line_kindlist where isnav=1) as a left join (select id,kindname,pinyin,opentypeids from sline_destinations) as b on a.kindid=b.id where find_in_set(b.opentypeids,1) order by a.displayorder asc';
        return DB::query(Database::SELECT, $sql)->execute()->as_array();
    }

    /**
     * 生成子站列表
     */
    public static function gen_web_list()
    {
        $webfile = APPPATH.'/cache/weblist.php';
        if (!file_exists($webfile))
        {
            $out = array();
            $arr = ORM::factory('destinations')
                ->where("iswebsite=1")
                ->get_all();
            foreach($arr as $row)
            {
                $out[$row['webprefix']]=array(
                    'webprefix'=>$row['webprefix'],
                    'weburl'=>$row['weburl'],
                    'kindname'=>$row['kindname'],
                    'webid'=>$row['id']
                );
            }
            if(!empty($out))
            {

                $weblist = "<?php defined('SYSPATH') or die('No direct script access.');". PHP_EOL . "\$weblist= ".var_export($out,true).";";
                $fp = fopen($webfile,'wb');
                flock($fp,3);
                fwrite($fp,$weblist);
                fclose($fp);
            }

        }

    }


    /**
     * 按栏目读取热门目的地
     * @param $typeid
     */
    public static function get_hot_dest($typeid=0,$offset=0,$row=30)
    {
        if($typeid==0)
        {
            $arr = ORM::factory('destinations')
                ->where('isopen=1 AND ishot=1')
                ->order_by("displayorder","ASC")
                ->offset($offset)
                ->limit($row)
                ->get_all();
        }
        else
        {
            $pinyin = ORM::factory('model',$typeid)->get('pinyin');
            if(!empty($pinyin))
            {
                //对应目的地表
                $table = 'sline_' . $pinyin . '_kindlist';
                $sql = "SELECT a.id,a.kindname,a.pinyin FROM `sline_destinations` a LEFT JOIN ";
                $sql .= "`$table` b ON (a.id=b.kindid) ";
                $sql .= "WHERE FIND_IN_SET($typeid,a.opentypeids) AND b.ishot=1 ";
                $sql .= "ORDER BY b.displayorder ASC ";
                $sql .= "LIMIT $offset,$row";
                $arr = DB::query(1,$sql)->execute()->as_array();

            }

        }
        return $arr;

    }

    /**
     * @param $offset
     * @param $row
     * @return mixed 顶级目的地列表
     */
    public static function get_top($offset, $row)
    {
        $arr = ORM::factory('destinations')
            ->where("pid=0 AND isopen=1")
            ->order_by('displayorder', 'ASC')
            ->offset($offset)
            ->limit($row)
            ->get_all();
        return $arr;

    }

    /**
     * @param $offset 偏移量
     * @param $row  条数
     * @param $pid  父级目的地id
     * @return mixed 下级目的地列表
     */
    public static function get_next($offset, $row, $pid)
    {
        $arr = ORM::factory('destinations')
            ->where("pid=$pid AND isopen=1")
            ->order_by('displayorder', 'ASC')
            ->offset($offset)
            ->limit($row)
            ->get_all();
        return $arr;
    }

    /**
     * @param $offset 偏移量
     * @param $row  条数
     * @param $pid  父级目的地id
     * @param $typeid  类型id
     * @return mixed 下级目的地列表
     */
    public static function get_dest($pid=0, $typeid=0, $offset, $row=10)
    {
        $arr = ORM::factory('destinations')
            ->where("isopen=1 AND pid=$pid AND FIND_IN_SET($typeid,opentypeids)")
            ->order_by('displayorder', 'ASC')
            ->offset($offset)
            ->limit($row)
            ->get_all();
        return $arr;
    }

    /**
     * @param $offset
     * @param $row
     * @return mixed 首页导航目的地
     */
    public static function get_index_nav($offset, $row)
    {
        $arr = ORM::factory('destinations')
            ->where("isnav=1 AND isopen=1")
            ->order_by('displayorder', 'ASC')
            ->offset($offset)
            ->limit($row)
            ->get_all();
        return $arr;
    }

    /**
     * @param $offset
     * @param $row
     * @param $typeid
     * @return array 栏目首页导航目的地
     */

    public static function get_channel_nav($offset, $row, $typeid)
    {
        $pinyin = ORM::factory('model', $typeid)->get('pinyin');
        $arr = array();
        if ($pinyin)
        {
            //对应目的地表
            $table = 'sline_' . $pinyin . '_kindlist';
            $sql = "SELECT a.id,a.kindname,a.pinyin FROM `sline_destinations` a LEFT JOIN ";
            $sql .= "`$table` b ON (a.id=b.kindid) ";
            $sql .= "WHERE FIND_IN_SET($typeid,a.opentypeids) AND b.isnav=1 ";
            $sql .= "ORDER BY b.displayorder ASC ";
            $sql .= "LIMIT $offset,$row";
            $arr = DB::query(Database::SELECT, $sql)->execute()->as_array();
        }
        return $arr;
    }

   /*
    * 获取目的地最后一级信息
    * */

    public static function  get_last_dest($kindlist)
    {
        $kindlistArr = explode(',', $kindlist);
        $maxdest = max($kindlistArr);
        if(empty($maxdest))
            return array();

        $sql = "SELECT	* FROM	sline_destinations WHERE id ={$maxdest}";
        $rows = DB::query(Database::SELECT, $sql)->execute()->as_array();
        if(count($rows) > 0)
            return $rows[0];
        else
            return array();
    }

    /*
    * 获取目的地通过拼音
    * */
    public static function get_dest_bypinyin($destpy)
    {
        if (!empty($destpy) && $destpy != 'all')
        {
            $rows = ORM::factory('destinations')->where("pinyin='$destpy' AND isopen=1")->get_all();
            if (count($rows) > 0)
                return $rows[0];
            else
                return array();
        } else
        {
            return array();
        }
    }

    /*
    * 获取目的地优化标题
    * */
    public static function search_seo($destpy, $typeid)
    {
        $result = array(
            'seotitle' => "",
            'keyword' => "",
            'description' => ""
        );

        if (!empty($destpy) && $destpy != 'all')
        {
            $dest = Model_Destinations::get_dest_bypinyin($destpy);
            $destId = $dest["id"];
            if (!empty($destId))
            {
                $seotitle = "";
                $model = ORM::factory("model", $typeid)->as_array();
                if (!empty($model['pinyin']))
                {
                    $kindlist_tablename = "{$model['pinyin']}_kindlist";
                    $info = ORM::factory($kindlist_tablename)->where("kindid", "=", $destId)->find()->as_array();
                    $seotitle = $info['seotitle'] ? $info['seotitle'] : $info['kindname'];
                }
                if (empty($seotitle))
                {
                    $info = ORM::factory('destinations', $destId)->as_array();
                    $seotitle = $info['seotitle'] ? $info['seotitle'] : $info['kindname'];
                }

                $result["seotitle"] = $seotitle;
                $result["keyword"] = $info["keyword"];
                $result["description"] = $info["description"];
            }
        } else
        {
            $info = Model_Nav::get_channel_info($typeid);
            $result["seotitle"] = $info['seotitle'] ? $info['seotitle'] : $info['m_title'];
            $result["keyword"] = $info["keyword"];
            $result["description"] = $info["description"];
        }

        return $result;
    }





}
