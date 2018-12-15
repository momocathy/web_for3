<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Notes extends Stourweb_Controller{

    /*
     * 评论总控制器
     * @author:netman
     * @data:2014-07-22
     * */

    public function before()
    {
        parent::before();
        $action = $this->request->action();
        if($action == 'index')
        {

            $param = $this->params['action'];

            $right = array(
                'read'=>'slook',
                'save'=>'smodify',
                'delete'=>'sdelete',
                'update'=>'smodify'
            );
            $user_action = $right[$param];
            if(!empty($user_action))
                Common::getUserRight('notes',$user_action);


        }
        else if($action == 'view')
        {
            Common::getUserRight('notes','slook');
        }
        else if($action == 'ajax_save')
        {
            Common::getUserRight('notes','smodify');
        }
        $this->assign('parentkey',$this->params['parentkey']);
        $this->assign('itemid',$this->params['itemid']);

    }
    public function action_index()
    {



        $action=$this->params['action'];
        if(empty($action))  //显示问答列表
        {
            $this->display('stourtravel/notes/list');
        }
        else if($action=='read')    //读取列表
        {
            $start=Arr::get($_GET,'start');
            $limit=Arr::get($_GET,'limit');
            $keyword=Arr::get($_GET,'keyword');

            $order='order by a.modtime desc';
            $sql="select a.*  from sline_notes as a  $order limit $start,$limit";
            $totalcount_arr=DB::query(Database::SELECT,"select count(*) as num from sline_notes a ")->execute()->as_array();
            $list=DB::query(Database::SELECT,$sql)->execute()->as_array();
            $new_list=array();
            foreach($list as $k=>$v)
            {
               // $v['productname'] = ORM::factory('question')->getProductName($v['articleid'],$v['typeid']);
                $v['nickname'] = Model_Comment::getMemberName($v['memberid']);
                //$v['modulename'] = Model_Comment::getPinlunModule($v['typeid']);
                $new_list[] = $v;
            }
            $result['total']=$totalcount_arr[0]['num'];
            $result['lists']=$new_list;
            $result['success']=true;
            echo json_encode($result);
        }
        else if($action=='save')   //保存字段
        {

        }
        else if($action=='delete') //删除某个记录
        {
            $rawdata=file_get_contents('php://input');
            $data=json_decode($rawdata);
            $id=$data->id;

            if(is_numeric($id)) //
            {
                $model=ORM::factory('notes',$id);
                $model->delete();
            }
        }
        else if($action=='update')//更新某个字段
        {
            $id=Arr::get($_POST,'id');
            $field=Arr::get($_POST,'field');
            $val=Arr::get($_POST,'val');

            if(is_numeric($id))
            {
                $model=ORM::factory('notes',$id);
            }
            if($model->id)
            {
                $model->$field=$val;
                $model->save();
                if($model->saved())
                    echo 'ok';
                else
                    echo 'no';
            }
        }

    }
    /**
     * 查看游记
     */
public function action_view()
{
  $id=$this->params['id'];
  $info = ORM::factory('notes')->where('id','=',$id)->find()->as_array();
  $info['kindlist_arr'] = ORM::factory('destinations')->getKindlistArr($info['kindlist']);//目的地数组
  $this->assign('info',$info);
  $this->display('stourtravel/notes/view');
}

/**
 * 游记审核
 */
public function action_ajax_save()
{
    $id = Arr::get($_POST,'id');
    $status = false;
    $kindlist = implode(',',Arr::get($_POST,'kindlist'));

    $model = ORM::factory('notes',$id);
    $model->reason= $_POST['reason'];
    if($_POST['status']!=-1){
        $model->reason='';
    }
    //赠送积分
    if($_POST['status']==1 && $_POST['issend']<1){
        $query = DB::query(Database::UPDATE,"Update `sline_member` set `jifen`=`jifen`+{$_POST['jifen']} where mid={$_POST['memberid']}")->execute();
        $jifen = ORM::factory('member_jifen_log');
        $jifen->memberid=$_POST['memberid'];
        $jifen->content="游记：{$_POST['title']},通过审核赠送积分{$_POST['jifen']}分";
        $jifen->jifen=$_POST['jifen'];
        $jifen->type=2;
        $jifen->addtime=time();
        $jifen->save();
        $model->issend ='1';

    }
    $model->status = $_POST['status'];
    $model->audittime = time();
    $model->kindlist = $kindlist;
    $model->finaldestid=empty($_POST['finaldestid'])?Model_Destinations::getFinaldestId(explode(',', $model->kindlist)):$_POST['finaldestid'];
    $model->save();
    if($model->saved())
    {
        $status = true;
    }
    echo json_encode(array('status'=>$status));
}



}