<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Hotel
 * @desc 酒店总控制器
 */
class Controller_Article extends Stourweb_Controller
{
    private $_typeid = 4;   //产品类型

    public function before()
    {
        parent::before();

        $channelname = Model_Nav::get_channel_name($this->_typeid);
        $this->assign('typeid',$this->_typeid);
        $this->assign('channelname',$channelname);
    }

    /**
     * 酒店首页
     */
    public function action_index()
    {
         $seoinfo = Model_Nav::get_channel_seo($this->_typeid);
         $this->assign('seoinfo',$seoinfo);
         $this->display('article/index');
    }

    /**
     * 详细页
     */
    public function action_show()
    {
        $aid = Common::remove_xss($this->request->param('aid'));
        $row = Model_Article::get_article_details($GLOBALS['sys_webid'], $aid);
        //点击率加一
        Product::update_click_rate($aid, $this->_typeid);
        if(count($row) <= 0)
        {
            Common::head404();
        }

        $row = Model_Article::get_article_attachinfo($row);
        $row = $row[0];

        $seoinfo = Product::seo($row);

        $this->assign('seoinfo',$seoinfo);
        $this->assign('info',$row);
        $this->display('article/show');
    }

    /**
     * 搜索结果
     */
    public function action_list()
    {
        //参数处理
        $paras = explode('-', Common::remove_xss($this->request->param('params')));

        $dest = $paras[0];
        $sorttype = empty($paras[1]) ? "0" : $paras[1];
        $attrid = empty($paras[2]) ? "0" : $paras[2];
        $keyword = Common::remove_xss($_GET["keyword"]);

        $destinfo = Model_Destinations::get_dest_bypinyin($dest);
        $destId = empty($destinfo["id"]) ? "0" : $destinfo["id"];
        $destname =$dest!='all' ? ORM::factory('destinations')->where("pinyin='$dest'")->find()->get('kindname') : '目的地';

        $page = Common::remove_xss(intval(Arr::get($_GET, 'page')));
        $page = $page < 1 ? 1 : $page;

        //获取seo信息
        $seo = Model_Destinations::search_seo($dest, $this->_typeid);
        $this->assign('seoinfo', $seo);
        $this->assign('destid', $destId);
        $this->assign('destname',$destname);
        $this->assign('sorttype', $sorttype);
        $this->assign('attrid', $attrid);
        $this->assign('keyword', $keyword);
        $this->assign('page', $page);

        $this->display('article/list');
    }

    /**
     * 搜索页
     */
    public function action_search()
    {
        $this->display('article/search');
    }

    /**
     * @return string|void
     */
    public function action_ajax_article_order()
    {
        if(!$this->request->is_ajax())return '';
        $offset =  Common::remove_xss(Arr::get($_GET,'offset'));
        $count =  Common::remove_xss(Arr::get($_GET,'count'));
        $havepic =  Common::remove_xss(Arr::get($_GET,'havepic'));

        $rows = Model_Article::get_article_order($offset, $count, $havepic);
        if(count($rows) <= 0)
        {
            echo json_encode(false);
            return;
        }
        $rows = Model_Article::get_article_attachinfo($rows);

        foreach($rows as &$row)
        {
            $row['litpic'] = Common::img($row['litpic']);
            $row['summary'] = Common::cutstr_html($row['summary'], 100);
        }

        echo json_encode(array('list'=>$rows));
    }

    /**
     * ajax请求 加载更多
     * @param string $pagesize
     * @return string|void
     */
    public function action_ajax_article_more($pagesize = '10')
    {
        if (!$this->request->is_ajax()) return '';
        $page = Common::remove_xss(Arr::get($_GET, 'page'));
        $offset = (intval($page) - 1) * $pagesize;
        $destid = Common::remove_xss(Arr::get($_GET, 'destid'));
        $sorttype = Common::remove_xss(Arr::get($_GET, 'sorttype'));
        $attrid = Common::remove_xss(Arr::get($_GET, 'attrid'));
        $keyword = Common::remove_xss(Arr::get($_GET, 'keyword'));

        $rows = Model_Article::search_article($destid, $attrid, $keyword, $offset, $pagesize, false, $sorttype);

        if (count($rows) <= 0)
        {
            echo json_encode(false);
            return;
        }
        $data = Model_Article::get_article_attachinfo($rows);

        foreach ($data as &$v)
        {
            $v['litpic'] = Common::img($v['litpic']);
            $v['summary'] = Common::cutstr_html($v['summary'], 30);
        }

        echo Product::list_search_format($data, $page);
    }
}