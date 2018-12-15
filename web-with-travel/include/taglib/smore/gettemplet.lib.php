<?php   if(!defined('SLINEINC')) exit('Request Error!');
 /**
 * 获取头部,底部公共标签
 *
 * @version        $Id: gettemplet.lib.php 1 9:29 2014.09.28 netman $
 * @package        Stourweb.Taglib
 * @copyright      Copyright (c) 2008 - 2014, Stourweb, Inc.
 * @link           http://www.stourweb.com
 */
 
require_once(SLINEINC.'/view.class.php');
function lib_gettemplet(&$ctag,&$refObj)
{
    global $dsql,$sys_webid;
	include(SLINEDATA."/webinfo.php"); 
    $attlist="pagename|";
    FillAttsDefault($ctag->CAttribute->Items,$attlist);
    extract($ctag->CAttribute->Items, EXTR_SKIP);

    $innertext = '';
    $revalue = '';
	$webid = $GLOBALS['sys_child_webid'];

	if($webid==0)
	{
	   $sql="select b.path from #@__page a left join #@__page_config b on a.id=b.pageid where a.pagename='$pagename' and b.isuse = 1";
	}
	else
	{
		$sql="select b.path from #@__site_page a left join #@__site_page_config b on a.id=b.pageid where a.pagename='$pagename' and b.isuse = 1 and webid='$webid'";
	
	}

    $row=$dsql->GetOne($sql);
    if(!empty($row['path']))
    {
        $innertext = "{sline:include file='uploadtemplets/".$row['path']."/index.htm'/}";
    }
    else
    {
        $innertext = "{sline:include file='public/".$pagename."_sys.htm'/}";
    }

      $pv = new View(0);
	  $pv->Fields['typeid'] = $refObj->Fields['typeid'] ;
      $pv->SetTemplet($innertext,'string');
      $revalue .= $pv->GetResult();

    return $revalue;
}
 



