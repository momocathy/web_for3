<?php defined('SYSPATH') or die('No direct access allowed.');

/*
 * 系统升级类(新版本)
 * */

class Model_SystemParts
{
    public static $coreSystemPartCode = 'core';
    public static $pcSystemPartCode = 'pc';
    public static $mobileSystemPartCode = 'mobile';

    public static function getSystemPart($partname, $partid = null)
    {
        $systemParts = self::getSystemParts();
        foreach ($systemParts as $systempartname => $systempartarr)
        {
            if ($systempartname == $partname)
            {
                if ($partid == null)
                    return $systempartarr;

                foreach ($systempartarr as $systempart)
                {
                    if ($systempart['id'] == $partid)
                        return $systempart;
                }
            }
        }
        return null;
    }


    public static function getSystemParts()
    {
        $result = array();
        $partsconfig = Common::getConfig('systemparts');
        foreach ($partsconfig as $partname => $partconfigarr)
        {
            $result[$partname] = array();
            foreach ($partconfigarr as $partconfig)
            {
                if (isset($partconfig['version_path']))
                {
                    $versionfile = BASEPATH . '/' . $partconfig['version_path'];
                    if (is_file($versionfile))
                    {
                        include($versionfile);
                        $result[$partname][] = Arr::merge($partconfig, array('pcode' => $pcode, 'cVersion' => $cVersion, 'versiontype' => $versiontype, 'pubdate' => $pubdate));
                    }
                }
            }
        }

        return $result;
    }

    public static function getCoreMajorVersion()
    {
        $majorVersion = '4.2';
        $coresystempart = self::getSystemPart(self::$coreSystemPartCode, '0');
        if ($coresystempart != null)
        {
            $verarr = explode('.', $coresystempart['cVersion']);
            $majorVersion = implode('.', array_slice($verarr, 0, 2));
        }
        return $majorVersion;
    }
}