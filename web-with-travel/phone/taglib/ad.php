<?php

/**
 * Created by Phpstorm.
 * User: netman
 * Date: 15-9-23
 * Time: 上午10:43
 * Desc: 广告获取标签
 */
class Taglib_Ad
{

    /*
     * 获取广告
     * @param 参数
     * @return array

   */
    public static function getad($params)
    {
        $default = array(
            'name' => '',
            'destid' => '0',
            'pc' => 0
        );
        $params = array_merge($default, $params);
        extract($params);
        $pc_where = $pc == 0 ? "AND is_pc='0'" : "AND is_pc='1'";
        $bool = preg_match('`^s_`', $name);
        $name = substr($name, 2);
        $pc_where .= $bool ? " AND is_system='1'" : " AND is_system='0'";
        $sql = "SELECT * FROM sline_advertise_5x WHERE CONCAT(prefix,'_',number) = '{$name}' AND is_show='1' {$pc_where}";
        if (!empty($destid))
        {
            $sql .= " AND FIND_IN_SET({$destid},kindlist)";
        }
        $ar = DB::query(1, $sql)->execute()->as_array();
        if (count($ar) <= 0)
        {
            return $ar;
        }
        $row = $ar[0];
        $row['aditems'] = array();
        $adsrc = unserialize($row['adsrc']);
        $adlink = unserialize($row['adlink']);
        $adname = unserialize($row['adname']);
        for ($i = 0; $i < count($adsrc); $i++)
        {
            $row['aditems'][$i] = array('adsrc' => $adsrc[$i], 'adlink' => $adlink[$i], 'adname' => $adname[$i]);
        }
        return $row;
    }
    /*
     * 顶部插件广告位
     * */
    public static function pluginad($params)
    {
        $default = array(
            'index' => 0,
            'adname' => 's_header_2,s_header_3,s_header_4,s_header_5,s_header_6,s_header_7',
            'pc' => 0

        );
        $params = array_merge($default, $params);
        extract($params);
        $pc_where = $pc == 0 ? "AND is_pc='0'" : "AND is_pc='1'";
        $adArr = explode(',',$adname);
        $tag = $adArr[$index];
        if(!empty($tag))
        {
            $bool = preg_match('`^s_`', $tag);
            $pc_where .= $bool ? " AND is_system='1'" : " AND is_system='0'";
            $name = substr($tag, 2);
            $sql = "SELECT * FROM sline_advertise_5x WHERE CONCAT(prefix,'_',number) = '$name' AND is_show='1' {$pc_where}";

            $ar = DB::query(1, $sql)->execute()->as_array();
            if (count($ar) <= 0)
            {
                return $ar;
            }
            $row = $ar[0];
            $row['aditems'] = array();
            $adsrc = unserialize($row['adsrc']);
            $adlink = unserialize($row['adlink']);
            $adname = unserialize($row['adname']);
            for ($i = 0; $i < count($adsrc); $i++)
            {
                $row['aditems'][$i] = array('adsrc' => $adsrc[$i], 'adlink' => $adlink[$i], 'adname' => $adname[$i]);
            }
            return $row;
        }
        else
        {
            return array();
        }





    }

} 