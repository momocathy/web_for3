<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Photo 相册
 */
class Controller_Photo extends Stourweb_Controller
{
    private $_typeid = 6;   //产品类型

    public function before()
    {
        parent::before();

        $channelname = Model_Nav::get_channel_name($this->_typeid);
        $this->assign('typeid', $this->_typeid);
        $this->assign('channelname', $channelname);
    }

    /**
     * 相册首页
     */
    public function action_index()
    {

        $seoinfo = Model_Nav::get_channel_seo($this->_typeid);
        $this->assign('seoinfo', $seoinfo);
        $this->assign('destpy', 'all');
        $this->assign('destname','目的地');
        $this->assign('attrid', '');
        $this->assign('page', 1);
        $this->assign('sorttype', 0);
        $this->display('photo/list');
    }

    /**
     * 相册列表
     */
    public function action_list()
    {
        //参数处理
        $params = Common::remove_xss($this->request->param('params'));
        $params = explode('-', $params);
        list($destPy, $attr, $page, $sorttype) = $params;
        $destname =$destPy!='all' && !empty($destPy) ? ORM::factory('destinations')->where("pinyin='$destPy'")->find()->get('kindname') : '目的地';

        $page = $page < 1 ? 1 : $page;
        //获取seo信息
        $seo = Model_Photo::search_seo($destPy);
        $this->assign('seoinfo',$seo);
        $this->assign('destpy', $destPy);
        $this->assign('destname',$destname);
        $this->assign('attrid', $attr);
        $this->assign('page', $page);
        $this->assign('sorttype',$sorttype);
        $this->display('photo/list');
    }

    /**
     * ajax请求 加载更多
     */
    public function action_ajax_photo_more()
    {
        $params = Common::remove_xss($this->request->param('params'));
        $params = explode('-', $params);
        list($destPy, $attr, $p,$order) = $params;
        if(strlen($order)==0){
            $order=0;
        }
        echo $this->list_data($destPy, $attr, $p,$order);
    }

    /**
     * 获取list 数据
     * @param $destPy
     * @param $attr
     * @param $p
     * @param bool|false $order
     * @return mixed
     */
    private function list_data($destPy, $attr, $page, $order)
    {
        $data = array();
        //目的地
        if ($destPy != all)
        {
            $destArr = Model_Destinations::get_dest_bypinyin($destPy);
            if (!empty($destArr['id']))
            {
                $data['kindlist'] = $destArr['id'];
            }
        }
        //属性
        if (!empty($attr))
        {
            $data['attrid'] = explode('_', $attr);
        }
        //分页
        $page = empty($page) ? 1 : $page;
        $data['offset'] = ($page - 1) * 10;
        //排序
        $data['order']=$order;
        $data = Model_Photo::photo_list($data);
        foreach ($data as &$v)
        {
            $v['litpic'] = Common::img($v['litpic'], 250, 185);
            $v['url'] = Common::get_web_url($v['webid']) . "/photos/show_{$v['aid']}.html";
            $v['description'] = Common::cutstr_html($v['description'],120);
        }
        return Product::list_search_format($data, $page);
    }

    /**
     * 相册详情
     */
    public function action_show()
    {
        $aid = Common::remove_xss($this->request->param('aid'));
        $row = Model_Photo::photo_detail($aid);
        //点击率加一
        Product::update_click_rate($aid, $this->_typeid);
        //picture
        $piclist = Model_Photo::photo_picture($row['id']);
        foreach ($piclist as &$v)
        {
            if (empty($v['description']))
            {
                $v['description'] = $row['content'];
            }
        }
        $row['piclist'] = $piclist;
        $seoInfo = Product::seo($row);
        $this->assign('info', $row);
        $this->assign('seoinfo', $seoInfo);
        $this->display('photo/show');
    }

}