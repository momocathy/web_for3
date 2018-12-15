<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Question extends ORM
{
    /*
        * 获取酒店
        * @param 参数
        * @return array

      */
    private static $basefield ='a.id,
                            a.content,
                            a.replycontent,
                            a.replytime,
                            a.nickname,
                            a.ip,
                            a.status,
                            a.memberid,
                            a.addtime,
                            a.qq,
                            a.webid,
                            a.phone,
                            a.weixin,
                            a.email,
                            a.title,
                            a.questype';

    /*
    * 搜索
    * */
    public static function search_question($status, $webid, $keyword, $offset, $row)
    {
        $sql = "SELECT ".self::$basefield." FROM `sline_question` a ";
        $sql.= "WHERE 1=1 ";
        if(!empty($webid))
            $sql.= "AND a.webid={$webid} ";
        if(!empty($status))
            $sql.= "AND a.status={$status} ";
        if(!empty($keyword))
            $sql.= "AND a.content like '%{$keyword}%' ";

        $sql.= "ORDER BY replytime desc ";
        $sql.= "LIMIT {$offset},{$row}";
        $arr = self::execute($sql);
        return $arr;
    }
    /*
    * 搜索
    * */
    public static function search_question_count($status, $webid, $keyword, $offset, $row)
    {
        $sql = "SELECT count(0) as num FROM `sline_question` a ";
        $sql.= "WHERE 1=1 ";
        if(!empty($webid))
            $sql.= "AND a.webid={$webid} ";
        if(!empty($status))
            $sql.= "AND a.status={$status} ";
        if(!empty($keyword))
            $sql.= "AND a.content like '%{$keyword}%' ";

        $sql.= "ORDER BY replytime desc ";
        $sql.= "LIMIT {$offset},{$row}";
        $arr = self::execute($sql);
        return $arr;
    }

    /*
    * 执行sql语句
    * */
    private static function execute($sql)
    {
        $arr = DB::query(1,$sql)->execute()->as_array();
        return $arr;
    }
}