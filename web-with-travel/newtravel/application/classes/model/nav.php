<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * 主导航模型
 * */

class Model_Nav extends ORM {

    protected  $_table_name = 'nav';

    /*
     * 获取主导航列表
     * @param int webid
     * @return array
     * */
    public function deleteClear()
    {
        $children=ORM::factory('nav')->where("pid={$this->id}")->find_all()->as_array();
        foreach($children as $child)
        {
            $child->deleteClear();
        }
        $this->delete();
    }
    public function getNav($webid)
    {
        $arr = $this->where('webid','=',$webid)
                    ->and_where('pid','=','0')
                    ->order_by('displayorder','asc')
                    ->get_all();
        return $arr;

    }
    /*
   * 保存主导航列表
   * @param int webid
   * @return array
   * */
    public function saveNav($data)
    {
        $shortname = Arr::get($data,'shortname');
        $displayorder = Arr::get($data,'displayorder');
        $linktitle = Arr::get($data,'linktitle');
        $kind = Arr::get($data,'kind');
        $id = Arr::get($data,'id');
        $isopen = Arr::get($data,'isopen');
        $url = Arr::get($data,'url');
        for($i=0;isset($shortname[$i]);$i++)
        {
            $obj = $this->where('id','=',$id[$i])->find();
            $obj->kind = $kind[$i];
            $obj->shortname = $shortname[$i];
            $obj->displayorder = $displayorder[$i];
            $obj->linktitle = $linktitle[$i];
            $obj->isopen = $isopen[$i];
            $obj->url = $url[$i];
            $obj->update();
            $obj->clear();
        }
    }



    /*
     * 获取未设置信息数量
     * @param string column
     * @param int webid
     * @return int count
     * */
    public function getUnsetCount($column,$webid)
    {


        $arr = $this->where("$column is null and webid=$webid and pid=0",'','') ->get_all();
        return intval(count($arr));//返回数量

    }

    public function myTest()
    {
        $sql = "select * from sline_nav where webid=0 and pid=0";
        $arr = $this->get_sql($sql);
        return $arr;
    }

	    /*
       * 保存手机导航
       * @param int webid
       * @return array
       * */
    public function save_mobile_nav($data)
    {
        $shortname = Arr::get($data,'shortname');
        $displayorder = Arr::get($data,'displayorder');
        $id = Arr::get($data,'id');
        $isopen = Arr::get($data,'isopen');
        $url = Arr::get($data,'url');

        for($i=0;isset($shortname[$i]);$i++)
        {

            $obj = ORM::factory('m_nav')->where("navid='$id[$i]'")->find();
            if(!$obj->loaded())
            {
                $obj = ORM::factory('m_nav');
                $obj->navid = $id[$i];
            }
            $obj->m_title = $shortname[$i];
            $obj->m_displayorder = $displayorder[$i] ? $displayorder[$i] : 9999;
            $obj->m_isopen = $isopen[$i];
            $obj->m_url = $url[$i];
            $obj->save();
            $obj->clear();
        }

    }

}