<?php
/**
 * Created by Phpstorm.
 * User: netman
 * Date: 15-9-23
 * Time: 上午10:43
 * Desc: 底部导航获取标签
 */

class Taglib_Flink {

    /*
     * 获取友情链接
     * @param 参数
     * @return array

   */
    public static function query($params)
    {
        $default=array(
            'flag'=>'all',
            'offset'=>0,
            'row'=>30,
            'typeid'=>0
        );
        $params=array_merge($default,$params);
        extract($params);
        $webid = $GLOBALS['sys_webid'];//站点id
        $typeid = $typeid ? $typeid : $GLOBALS['typeid'];
        $typeid = $typeid ? $typeid : 0;
        if($flag=="all") //全部
        {
            $sql="SELECT sitename AS title,siteurl AS url FROM `sline_yqlj` WHERE webid='$webid'   ORDER BY addtime DESC LIMIT {$offset},{$row}";
        }

        else //按栏目读取
        {
            $sql ="SELECT sitename AS title,siteurl AS url FROM `sline_yqlj` ";
            $sql.="WHERE webid='$webid' AND LOCATE($typeid,address) ";
            $sql.="ORDER BY addtime DESC LIMIT {$offset},{$row}";
        }
        $ar = DB::query(1,$sql)->execute()->as_array();


        return $ar;
    }



} 