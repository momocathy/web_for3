<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Question 问答
 */
class Controller_Question extends Stourweb_Controller
{
    private $_typeid = 10;   //产品类型

    public function before()
    {
        parent::before();

        $channelname = Model_Nav::get_channel_name($this->_typeid);
        $this->assign('typeid',$this->_typeid);
        $this->assign('channelname',$channelname);
    }
    /**
     * 首页
     */
    public function action_index()
    {
        $seoinfo = Model_Nav::get_channel_seo($this->_typeid);
        $page = Common::remove_xss(intval(Arr::get($_GET, 'page')));
        $page = $page < 1 ? 1 : $page;
        $this->assign('seoinfo',$seoinfo);
        $this->assign('page',$page);
        $this->display('question/index');
    }

    /**
     * 首页
     */
    public function action_add()
    {
        $seoinfo = Model_Nav::get_channel_seo($this->_typeid);
        $this->assign('seoinfo',$seoinfo);
        $this->display('question/add');
    }

    public function action_ajax_checkValidateCode()
    {
        $validateCode = Common::remove_xss(Arr::get($_POST, 'ValidateCode'));
        if(Captcha::valid($validateCode))
            echo "true";
        else
            echo "false";
    }

    public function action_save()
    {
        $title = Common::remove_xss(Arr::get($_POST, 'txtTitle'));
        $content = Common::remove_xss(Arr::get($_POST, 'txtContent'));
        $nickname = Common::remove_xss(Arr::get($_POST, 'txtNickname'));
        if(empty($nickname))
            $nickname = "匿名";

        $validateCode = Common::remove_xss(Arr::get($_POST, 'txtValidateCode'));
        $tel = Common::remove_xss(Arr::get($_POST, 'txtTel'));

        if(!Captcha::valid($validateCode))
        {
            Common::message(array('message' => __("error_code"), 'jumpUrl' => $this->cmsurl. 'questions/add'));
            exit;
        }
        Common::session('captcha_response', null);

        $validataion = Validation::factory($this->request->post());
        $validataion->rule('txtTitle', 'not_empty');
        $validataion->rule('txtContent', 'not_empty');
        $validataion->rule('txtTel', 'not_empty');
        $validataion->rule('txtTel', 'phone');
        if (!$validataion->check())
        {
            $error = $validataion->errors();
            $keys = array_keys($error);
            if($keys[0] == 'txtTitle')
            {
                Common::message(array('message' => __("error_question_title_not_empty"), $this->cmsurl . 'questions/add'));
            }
            elseif($keys[0] == 'txtContent')
            {
                Common::message(array('message' => __("error_question_content_not_empty"), $this->cmsurl . 'questions/add'));
            }
            elseif($keys[0] == 'txtTel')
            {
                Common::message(array('message' => __("error_linktel_phone"), $this->cmsurl . 'questions/add'));
            }
            else
            {
                Common::message(array('message' => __("error_{$keys[0]}_{$error[$keys[0]][0]}"), $this->cmsurl . 'questions/add'));
            }
            exit;
        }

        list($insert_id, $total_rows) = DB::insert('question', array('content', 'nickname', 'ip','status', 'addtime', 'webid', 'phone', 'title','questype'))
            ->values(array($content, $nickname, Common::GetIP(), '0', time(), $GLOBALS['sys_webid'], $tel, $title,'1'))
            ->execute();
        if ($total_rows)
        {
            Common::message(array('message' => __("error_question_success_add"), 'jumpUrl' => $this->cmsurl . 'questions'));
        }
        else
        {
            Common::message(array('message' => __("error_question_error_add"), 'jumpUrl' => $this->cmsurl . 'questions/add'));
        }
    }

    /**
     * 首页
     */
    public function action_ajax_question_search($pagesize=10)
    {
        if (!$this->request->is_ajax()) return '';
        $page = Common::remove_xss(intval(Arr::get($_GET, 'page')));
        $page = $page < 1 ? 1 : $page;
        $offset = (intval($page) - 1) * $pagesize;
        //$status = Common::remove_xss(Arr::get($_GET, 'status'));
        //$webid = Common::remove_xss(Arr::get($_GET, 'webid'));
        $keyword = Common::remove_xss(Arr::get($_GET, 'keyword'));

        $data = Model_Question::search_question(1, 0, $keyword, $offset, $pagesize);
        $count = Model_Question::search_question_count(1, 0, $keyword, 0, 1);

        if ($count <= 0)
        {
            echo json_encode(false);
            return;
        }

        foreach($data as &$v)
        {
            if(!empty($v['replytime']))
            {
                $v['replytime'] = date('Y-m-d', $v['replytime']);
            }
            if(!empty($v['addtime']))
            {
                $row['addtime'] = date('Y-m-d', $v['addtime']);
            }
        }
        if(count($data) < $pagesize)
        {
            $page = -1;
        }
        else
        {
            $page++;
        }
        echo json_encode(array('list' => $data, 'count' => $count[0]['num'], 'page' => $page));
    }
}