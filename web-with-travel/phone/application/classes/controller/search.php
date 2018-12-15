<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Search extends Stourweb_Controller{

    public static $typeArr=array(
        '1'=>array('typeid'=>1,'channelname'=>'线路'),
        '2'=>array('typeid'=>2,'channelname'=>'酒店'),
        '3'=>array('typeid'=>3,'channelname'=>'车辆'),
        '4'=>array('typeid'=>4,'channelname'=>'攻略'),
        '5'=>array('typeid'=>5,'channelname'=>'门票'),
        '6'=>array('typeid'=>6,'channelname'=>'相册'),
        '8'=>array('typeid'=>8,'channelname'=>'签证'),
        '13'=>array('typeid'=>13,'channelname'=>'团购')
    );
    public function before()
    {
        parent::before();
    }
    public function action_index()
    {
        $keyword = Common::remove_xss(trim(Arr::get($_GET, 'keyword')));
        $page = intval(Arr::get($_GET, 'page'));
        $typeid = intval(Arr::get($_GET, 'typeid'));
        $page = $page < 1 ? 1 : $page;

        $count = $this->get_result_count($keyword, 0);
        $typeArr = NULL;
        //全部
        $temp['typeid'] = 0;
        $temp['channelname'] = '全部';
        $temp['count'] = $count;
        $typeArr[] = $temp;
        //具体栏目搜索结果
        $channel = Model_Nav::get_all_m_channel();
        foreach (self::$typeArr AS $v)
        {
            foreach ($channel AS $v2)
            {
                if($v['typeid'] == $v2['typeid'])
                {
                    $temp = $v;
                    $temp['count'] = $this->get_result_count($keyword, $v['typeid']);
                    $typeArr[] = $temp;
                }
            }
        }

        $this->assign('typeid',$typeid);
        $this->assign('keyword',$keyword);
        $this->assign('page',$page);
        $this->assign('typeArr',$typeArr);
        $this->display('search/index');
    }

    /**
     * ajax请求 加载更多
     */
    public function action_ajax_search_more()
    {
        $keyword = Common::remove_xss(trim(Arr::get($_GET,'keyword')));
        $page = intval(Arr::get($_GET,'page'));
        $typeid = intval(Arr::get($_GET,'typeid'));
        $list = $this->get_result($page,$keyword,$typeid);
        $count = $this->get_result_count($keyword, 0);

        $typeArr = NULL;
        //全部
        $temp['typeid'] = 0;
        $temp['channelname'] = '全部';
        $temp['count'] = $count;
        $typeArr[] = $temp;
        //具体栏目搜索结果
        $channel = Model_Nav::get_all_m_channel();
        foreach (self::$typeArr AS $v)
        {
            foreach ($channel AS $v2)
            {
                if($v['typeid'] == $v2['typeid'])
                {
                    $temp = $v;
                    $temp['count'] = $this->get_result_count($keyword, $v['typeid']);
                    $typeArr[] = $temp;
                }
            }
        }
        $page = count($list)>0 ? intval($page)+1 : -1;
        echo json_encode(array('list'=>$list, 'typeArr'=>$typeArr, 'page'=>$page));
    }

    /**
     * @param $page
     * @return mixed
     * 获取搜索结果
     */

    public function get_result($page,$keyword,$typeid=0)
    {
        $pageSize=10;
        $page=empty($page)?1:$page;
        $offset=($page-1)*$pageSize;
        $valueArr=array();
        $w="title like :keyword";
        $valueArr[':keyword']='%'.$keyword.'%';
        if(!empty($typeid))
        {
            $w.=" and typeid=:typeid";
            $valueArr[':typeid']=$typeid;
        }
        $sql="SELECT * FROM `sline_search` WHERE $w LIMIT $offset,$pageSize";
        $query = DB::query(Database::SELECT,$sql)->parameters($valueArr);
        $list=$query->execute()->as_array();
        foreach($list as $k=>$v)
        {
            $list[$k]['url'] = $this->getUrl($v['webid'],$v['aid'],$v['typeid']);
            $list[$k]['litpic'] = !empty($v['litpic']) ? Common::img($v['litpic']) : '';
            $list[$k]['channelname'] = self::$typeArr[$v['typeid']]['channelname'];
            $list[$k]['description'] = Common::cutstr_html($v['description'],50);
        }
        return $list;
    }

    /**
     * @param $keyword
     * @param int $typeid
     * @return mixed
     */
    public function get_result_count($keyword, $typeid=0)
    {
        $typeArr=array(
            '0'=>array('typeid'=>0,'tablename'=>'sline_search'),
            '1'=>array('typeid'=>1,'tablename'=>'sline_line'),
            '2'=>array('typeid'=>2,'tablename'=>'sline_hotel'),
            '3'=>array('typeid'=>3,'tablename'=>'sline_car'),
            '4'=>array('typeid'=>4,'tablename'=>'sline_article'),
            '5'=>array('typeid'=>5,'tablename'=>'sline_spot'),
            '6'=>array('typeid'=>6,'tablename'=>'sline_photo'),
            '8'=>array('typeid'=>8,'tablename'=>'sline_visa'),
            '13'=>array('typeid'=>13,'tablename'=>'sline_tuan')
        );

        $valueArr=array();
        $w="title like :keyword";
        $valueArr[':keyword']='%'.$keyword.'%';

        $sql = "SELECT count(0) as num FROM `".$typeArr[$typeid]['tablename']."` WHERE $w ";
        $query = DB::query(Database::SELECT,$sql)->parameters($valueArr);
        $data = $query->execute()->as_array();
        return $data[0]['num'];
    }

    /**
     * @param $aid
     * @param $typeid
     * @return string
     * 获取产品地址.
     */

    public function getUrl($webid,$aid,$typeid)
    {
        $cmsUrl=Common::get_web_url($webid);
        $url='';
        switch($typeid)
        {
            case 1:
                $url=$cmsUrl.'/lines/show_'.$aid.'.html';
                break;
            case 2:
                $url=$cmsUrl.'/hotels/show_'.$aid.'.html';
                break;
            case 3:
                $url=$cmsUrl.'/cars/show_'.$aid.'.html';
                break;
            case 4:
                $url=$cmsUrl.'/raiders/show_'.$aid.'.html';
                break;
            case 5:
                $url=$cmsUrl.'/spots/show_'.$aid.'.html';
                break;
            case 6:
                $url=$cmsUrl.'/photos/show_'.$aid.'.html';
                break;
            case 8:
                $url=$cmsUrl.'/visa/show_'.$aid.'.html';
                break;
            case 13:
                $url=$cmsUrl.'/tuan/show_'.$aid.'.html';
                break;

        }
        return $url;
    }


}