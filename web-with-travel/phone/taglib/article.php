<?php
/**
 * Created by Phpstorm.
 * User: netman
 * Date: 15-9-23
 * Time: 上午10:43
 * Desc: 广告获取标签
 */

class Taglib_Article {



    /**
     * @param $params
     * @return mixed
     * @description 标签接口
     */
    public static function query($params)
    {
        $default=array(
            'flag'=>'',
            'destid'=>0,
            'offset'=>0,
            'row'=>'10',
            'havepic'=>false,
        );
        $params=array_merge($default,$params);
        extract($params);
        switch($flag)
        {
            case 'new':
                $list = Model_Article::get_article_new($offset,$row,$havepic);
                break;
            case 'order':
                $list = Model_Article::get_article_order($offset,$row,$havepic);
                break;
            case 'mdd':
                $list = Model_Article::get_article_bymdd($offset,$row,$destid,$havepic);
        }

        return Model_Article::get_article_attachinfo($list);

    }

} 