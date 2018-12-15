<?php defined('SYSPATH') or die('No direct script access.');

/*
 * author:netman
 * description:upgrade
 * date:2015-02-13
 * qq:87482723
 * */

class Controller_Upgrade extends Stourweb_Controller
{
    /*
     * 增值应用
     * */
    public function before()
    {
        parent::before();

        $this->assign('parentkey', $this->params['parentkey']);
        $this->assign('itemid', $this->params['itemid']);
    }

    private $_systemConfig = null;

    private function getSystemConfig($configkey)
    {
        if ($this->_systemConfig == null)
            $systemConfig = ORM::factory('sysconfig')->getConfig(0);
        return $systemConfig[$configkey];
    }

    private function currentSystempartCode()
    {
        $part = $this->params['systempart'];
        if (empty($part))
            return null;
        return $part;
    }

    private function currentSystempart()
    {
        $part = $this->currentSystempartCode();

        if ($part == Model_SystemParts::$coreSystemPartCode)
            return $this->getCoreSystemPart();
        if ($part == Model_SystemParts::$pcSystemPartCode)
            return $this->getPcSystemPart();
        if ($part == Model_SystemParts::$mobileSystemPartCode)
            return $this->getMobileSystemPart();

        return null;
    }

    private function getCoreSystemPart()
    {
        return Model_SystemParts::getSystemPart(Model_SystemParts::$coreSystemPartCode, '0');
    }

    private function getPcSystemPart()
    {
        return Model_SystemParts::getSystemPart(Model_SystemParts::$pcSystemPartCode, $this->getSystemConfig('cfg_pc_version'));
    }

    private function getMobileSystemPart()
    {
        return Model_SystemParts::getSystemPart(Model_SystemParts::$mobileSystemPartCode, $this->getSystemConfig('cfg_mobile_version'));
    }

    private function getAllSystemPartVersionInfo()
    {
        $result = array();

        $coreversion = $this->getCoreSystemPart();
        if ($coreversion != null)
            $result[Model_SystemParts::$coreSystemPartCode] = array('caption' => '后台版本', 'versioninfo' => $coreversion);
        else
            return $result;

        if ($this->getSystemConfig('cfg_pc_upgrade') == '1')
        {
            $pcversion = $this->getPcSystemPart();
            if ($pcversion != null)
                $result[Model_SystemParts::$pcSystemPartCode] = array('caption' => '电脑版本', 'versioninfo' => $pcversion);
        }

        if ($this->getSystemConfig('cfg_mobile_upgrade') == '1')
        {
            $mobileversion = $this->getMobileSystemPart();
            if ($mobileversion != null)
                $result[Model_SystemParts::$mobileSystemPartCode] = array('caption' => '移动版本', 'versioninfo' => $mobileversion);
        }

        return $result;
    }

    //系统升级页
    public function action_index()
    {
        $allSystemPartVersion = $this->getAllSystemPartVersionInfo();
        $this->assign('version_info', $allSystemPartVersion);

        if (array_key_exists(Model_SystemParts::$coreSystemPartCode, $allSystemPartVersion))
        {
            $model = new Model_Upgrade3();
            $model->initialise($allSystemPartVersion[Model_SystemParts::$coreSystemPartCode]['versioninfo']);
            $model->releaseFeedback();
        }

        $this->display('stourtravel/upgrade/index');
    }

    //ajax检测所有系统组件版本与更新( 首页使用)
    public function action_ajax_check_allSystemPart_update()
    {
        $result = array();

        $allSystemPartVersion = $this->getAllSystemPartVersionInfo();
        $model = new Model_Upgrade3();
        foreach($allSystemPartVersion as $key => $systemPartVersion)
        {
            $model->initialise($systemPartVersion['versioninfo']);

            $info = $model->getNewVersion();
            if ($info['Success'] != 1)
            {
                $info = $model->checkNewestPatch();
            }
            if ($info['Success'] == 1 && count($info['Data']) > 0)
            {
                $newinfo['desc'] = $info['Data'][0]['Description'];
                $newinfo['pubdate'] = Common::myDate('Y-m-d', strtotime($info['Data'][0]['ReleaseDate']));
                $newinfo['version'] = $info['Data'][0]['Version'];

                $allSystemPartVersion[$key]['newestVersioninfo'] = $newinfo;
            }

            $allSystemPartVersion[$key]['systemPartCode'] = $key;
            $result[] = $allSystemPartVersion[$key];
        }

        echo json_encode($result);
    }

    public function action_ajax_current_version()
    {
        $out = array();

        $currentPart = $this->currentSystempart();
        if ($currentPart == null)
        {
            $out['myversion'] = '0.0.0.0';
            echo json_encode($out);
            return;
        }

        $out['myversion'] = $currentPart['cVersion'];

        echo json_encode($out);
    }

    //ajax检测版本权限(正版检测)
    public function action_ajax_check_right()
    {
        $currentPart = $this->currentSystempart();
        if ($currentPart == null)
        {
            $out = array('status' => '0');
            echo json_encode($out);
            return;
        }

        $model = new Model_Upgrade3();
        $model->initialise($currentPart);
        $status = $model->checkRightV();
        $out = array('status' => $status);
        echo json_encode($out);
    }


    /*
     * 升级页面使用
     * 检测升级包(获取包括已升级和未升级的包的列表)
     *
     * */
    public function action_ajax_version()
    {
        $version = array();

        $currentPart = $this->currentSystempart();
        if ($currentPart == null)
        {
            echo json_encode($version);
            return;
        }

        $model = new Model_Upgrade3();
        $model->initialise($currentPart);
        $info = $model->getNewVersion();
        if ($info['Success'] != 1)
        {
            $info = $model->checkNewestPatch();
        }
        if ($info['Success'] == 1)
        {
            foreach ($info['Data'] as $ver)
            {
                $version[] = $this->generateVersionInfo($ver, false);
            }
        }

        $hasnum = count($version);
        if ($hasnum < 20)
        {
            $oldversion = $model->getOldPatch(20 - $hasnum);
            if ($oldversion['Success'] == 1)
            {
                foreach ($oldversion['Data'] as $ver)
                {
                    $version[] = $this->generateVersionInfo($ver, true);
                }

            }
        }
        print_r(json_encode($version)); //输出版本信息
    }

    private function generateVersionInfo($versiondata, $updatestatus)
    {
        if ($versiondata['Status'] == 3)
            $ver_status = '(内测)';
        elseif ($versiondata['Status'] == 2)
            $ver_status = '(公测)';
        else
            $ver_status = '';

        $ar = array(
            'desc' => Model_Upgrade3::genDesc($versiondata['Description']),
            'pubdate' => Common::myDate('Y-m-d', strtotime($versiondata['ReleaseDate'])),
            'version' => $versiondata['Version'] . $ver_status,
            'filesize' => Model_Upgrade3::format_bytes($versiondata['FileSize']),
            'status' => $updatestatus == true ? '已更新' : '未更新'
        );
        return $ar;
    }

    //绑定授权ID
    public function action_bind()
    {
        $model = new Model_Upgrade3();
        $serial = $model->getSerialnumber();
        $this->assign('licenseid', $serial);
        $this->display('stourtravel/upgrade/bind');
    }

    //绑定ID
    public function action_ajax_bind_license()
    {
        $licenseid = Arr::get($_POST, 'licenseid');
        Model_Upgrade3::rewriteLicense($licenseid);

        $flag = $this->checkLicense();
        if ($flag)
        {
            echo json_encode(array('status' => 1, 'msg' => '序列号绑定成功'));
        } else
        {
            echo json_encode(array('status' => 0, 'msg' => '序列号错误'));
        }
    }

    /*
     * ajax执行升级(每次只升级一个升级包)
     * */
    public function action_ajax_upgrade()
    {
        set_time_limit(0);

        $out = array();
        if (!$this->checkdir()) //检测目录写权限
        {
            $out['status'] = 0;
            $out['msg'] = '没有权限操作,请打开网站目录写权限'; //目录无写权限
            echo json_encode($out);
            exit;

        }
        if (!$this->checkLicense()) //检测序列号是否正确
        {
            $out['status'] = 0;
            $out['msg'] = '序列号验证失败,请检查序列号'; //序列号错误
            $out['type'] = 'license_err';
            echo json_encode($out);
            exit;

        }

        $currentsystempart = $this->currentSystempart();
        if ($currentsystempart == null || $this->getCoreSystemPart() == null)
        {
            $out['status'] = 0;
            $out['msg'] = '加载系统组件失败,请检查系统是否完整';
            echo json_encode($out);
            exit;
        }

        $model = new Model_Upgrade3();
        $model->initialise($currentsystempart);
        $info = $model->getNewVersion();
        if ($info['Success'] == 1)
        {
            if (!empty($info['Data']))
            {
                $versionlist = array_reverse($info['Data']);
                $ver = $versionlist[0];

                if ($this->currentSystempartCode() != 'core') //非内核系统升级，检查依赖性
                {
                    if ($this->exists_noupgrade_in_coresystem($ver['Status']))
                    {
                        $out['status'] = 3;
                        $out['msg'] = "存在未升级的 内核 {$ver['StatusName']} ，需要先升级完他们才能执行此操作，您现在要升级他们吗？";
                        echo json_encode($out);
                        exit;
                    }
                }

                if (!$this->is_internalbeta_site() && $ver['Status'] == 2) //公测补丁，需要用户确认
                {
                    if ($this->params['confirmbetaversion'] != $ver['Version']) //公测补丁未被用户确认
                    {
                        $out['status'] = 2;
                        $out['betaversion'] = $ver['Version'];
                        $out['msg'] = "补丁 {$ver['Version']} 为 {$ver['StatusName']} ，还在测试完善当中，可能存在未知问题，您确定要升级此补丁吗？";
                        echo json_encode($out);
                        exit;
                    }
                }

                $downloadurl = $ver['Url'];
                $savepath = APPPATH . "/data/patch/"; //更新包存储路径
                $filename = $savepath . $ver['Name'] . '.zip';
                $unzippath = BASEPATH . '/data/upgradetmp/' . $ver['Name']; //解压路径
                $this->clearUpgradeTmp($unzippath);
                $model->getToken();
                $ok = Common::downloadFile($downloadurl, $filename);
                if ($ok) //下载文件成功
                {
                    include(PUBLICPATH . '/vendor/zipfolder.php');
                    $archive = new ZipFolder();

                    $archive->setLoadPath(dirname($filename) . '/');
                    $archive->setFile(basename($filename));
                    $archive->setSavePath(dirname($unzippath) . '/');

                    $extractResult = $archive->openZip();

                    if (!$extractResult || Common::isEmptyDir($unzippath))
                    {
                        $out['status'] = 0;
                        $out['msg'] = '升级文件解压失败,升级文件损坏或网站目录及子目录无写权限'; //目录无写权限
                        echo json_encode($out);
                        exit;
                    } else
                    {
                        $archive->eraseZip();
                    }

                    $replaceBackgroundDirResult = $this->replaceBackgroundDir($unzippath);
                    if ($replaceBackgroundDirResult['success'] == false)
                    {
                        $out['status'] = 0;
                        $out['msg'] = '变更后台目录失败,' . $replaceBackgroundDirResult['errormsg'];
                        echo json_encode($out);
                        exit;
                    }

                    $backupOriginalResult = $this->backupOriginalFile($unzippath);
                    if ($backupOriginalResult['success'] == false)
                    {
                        $out['status'] = 0;
                        $out['msg'] = '备份文件失败,' . $backupOriginalResult['errormsg'];
                        echo json_encode($out);
                        exit;
                    }

                    $dataBaseaPatch = $this->generateDataBaseaPatch($unzippath);
                    if ($dataBaseaPatch == false)
                    {
                        $out['status'] = 0;
                        $out['msg'] = '准备数据库补丁失败';
                        echo json_encode($out);
                        exit;
                    }

                    $upgradeFilesResult = Common::xCopy($unzippath, BASEPATH, true);
                    if ($upgradeFilesResult['success'] == false)
                    {
                        $out['status'] = 0;
                        $out['msg'] = '覆盖文件失败,' . $upgradeFilesResult['errormsg'];
                        echo json_encode($out);
                        exit;
                    }

                    //数据库升级
                    if (file_exists($dataBaseaPatch))
                    {
                        $url = str_ireplace(BASEPATH, $GLOBALS['cfg_basehost'], $dataBaseaPatch);
                        $flag = Common::http($url);
                        if (strlen($flag) > 6)
                        {
                            //恢复备份文件
                            Common::xCopy($backupOriginalResult['backupdir'], BASEPATH, true);
                            //清理升级临时目录
                            $this->clearUpgradeTmp($unzippath);

                            $out['status'] = 0;
                            $out['msg'] = '数据库升级失败,请检查' . $flag; //数据库升级有错误,执行失败
                            echo json_encode($out);
                            exit;
                        }
                    }

                    //反馈版本信息
                    $regstatus = $model->regUpgradeStatus($downloadurl);
                    //版本包升级成功
                    if ($regstatus['Success'] == 1)
                    {
                        //写版本
                        $pubdate = Common::myDate('Y-m-d', strtotime($ver['ReleaseDate']));
                        $version = $ver['Version'];
                        $beta = $ver['Status'] != 1 ? 1 : 0;
                        if(!Model_Upgrade3::rewriteVersion(BASEPATH . '/' . $currentsystempart['version_path'], $currentsystempart['pcode'], $version, $beta, $pubdate))
                        {
                            $out['status'] = 0;
                            $out['msg'] = '更新本地版本文件失败!';
                            echo json_encode($out);
                            exit;
                        }

                        //清理升级临时目录
                        $this->clearUpgradeTmp($unzippath);

                        $out['status'] = 1;
                        $out['currentversion'] = $version;
                        echo json_encode($out);
                        exit;
                    } else
                    {
                        $out['status'] = 0;
                        $out['msg'] = '更新服务器登记版本失败!';
                        echo json_encode($out);
                        exit;
                    }
                } else
                {
                    $out['status'] = 0;
                    $out['msg'] = '下载升级包失败!';
                    echo json_encode($out);
                    exit;
                }

            } else
            {
                $out['status'] = 0;
                $out['msg'] = '已经升级到最新版本!';
                echo json_encode($out);
                exit;
            }

        } else
        {
            $out['status'] = 0;
            $out['msg'] = '获取新版本信息失败!';
            echo json_encode($out);
            exit;
        }

    }

    private function exists_noupgrade_in_coresystem($patchstatus)
    {
        $model = new Model_Upgrade3();
        $model->initialise($this->getCoreSystemPart());
        $info = $model->getNewVersion();
        if (!$info['Success'])
            return true;

        $newversionlist = $info['Data'];
        foreach ($newversionlist as $newversion)
        {
            if ($newversion['Status'] <= $patchstatus)
                return true;
        }
        return false;
    }

    private function is_internalbeta_site()
    {
        $model = new Model_Upgrade3();
        $result = $model->isInternalTestAppIdentity();

        return $result['Data'];
    }

    private function clearUpgradeTmp($unzippath)
    {
        Common::rrmdir(dirname($unzippath));
    }

    private function backupOriginalFile($unzippath)
    {
        $backupOriginalDir = $unzippath . '_backupdriginal';

        if (!is_dir($backupOriginalDir) && !mkdir($backupOriginalDir, 0777, true))
        {
            return array('success' => false, 'errormsg' => "创建备份目录 $backupOriginalDir 失败");
        }

        $fileManifest = $unzippath . '/FileManifest.txt';
        if (!file_exists($fileManifest))
            return array('success' => true, 'backupdir' => $backupOriginalDir);

        $fp = fopen($fileManifest, 'r');
        if (!$fp)
        {
            return array('success' => false, 'errormsg' => "打开补丁清单文件 $fileManifest 失败");
        }

        $result = array('success' => true, 'backupdir' => $backupOriginalDir);
        while (!feof($fp))
        {
            $filename = rtrim(fgets($fp));
            if (empty($filename) || strtolower($filename) == 'sql.php')
                continue;

            $filename = str_ireplace('newtravel', $GLOBALS['cfg_backdir'], $filename);
            $fromfile = BASEPATH . '/' . $filename;
            $tofile = $backupOriginalDir . '/' . $filename;
            if (!is_file($fromfile))
                continue;

            if (!is_writable($fromfile))
            {
                $result = array('success' => false, 'errormsg' => "文件 $fromfile 不可写");
                break;
            }
            $todir = dirname($tofile);
            if (!is_dir($todir) && !mkdir($todir, 0777, true))
            {
                $result = array('success' => false, 'errormsg' => "创建目录 $todir 失败");
                break;
            }
            if (!copy($fromfile, $tofile))
            {
                $result = array('success' => false, 'errormsg' => "拷贝文件 $fromfile 到 $tofile 失败");
                break;
            }
        }

        fclose($fp);
        return $result;
    }

    private function generateDataBaseaPatch($unzippath)
    {
        $databasePatchDir = $unzippath . '_databasepatch';

        if (!is_dir($databasePatchDir) && !mkdir($databasePatchDir, 0777, true))
            return false;

        $databasePatch = $databasePatchDir . '/sql.php';
        $fromfile = $unzippath . '/sql.php';
        if (!file_exists($fromfile))
            return $databasePatch;

        if (!rename($fromfile, $databasePatch))
            return false;

        return $databasePatch;
    }

    /*
     * 备份数据库
     *
     * */
    public function action_ajax_backup_database()
    {
        set_time_limit(0);

        $back = new Model_Backup();
        $back->backupAll();
        echo json_encode(array('status' => true));
    }

    //检测是否有写入权限
    private function checkdir()
    {
        $flag = 0;
        $filename = BASEPATH . '/stcms.txt';
        $fp = @fopen($filename, 'w');
        if ($fp)
        {
            @fclose($fp);
            @unlink($filename);
            $flag = 1;
        }
        return $flag;
    }

    //检测license是否有效
    private function checkLicense()
    {
        $flag = 0;

        $corepart = $this->getCoreSystemPart();
        if ($corepart == null)
            return $flag;

        $model = new Model_Upgrade3();
        $model->initialise($corepart);
        $info = $model->getLastPatch();
        if ($info['Success'] == 1)
        {
            $flag = 1;
        }
        return $flag;
    }

    //后台目录替换
    private function replaceBackgroundDir($unzippath)
    {
        $result = array('success' => true, 'errormsg' => "");
        $backdir = $GLOBALS['cfg_backdir'] ? $GLOBALS['cfg_backdir'] : 'newtravel';

        if (strtoupper($backdir) != strtoupper('newtravel') && is_dir($unzippath))
        {
            $dh = opendir($unzippath);
            if (!$dh)
            {
                $result['success'] = false;
                $result['errormsg'] = "打开目录{$unzippath}失败";
                return $result;
            }

            while ($object = readdir($dh))
            {
                if ($object != "." && $object != "..")
                {
                    $fullname = $unzippath . "/" . $object;
                    if (is_dir($fullname))
                    {
                        if (strtoupper($object) == strtoupper('newtravel'))
                        {
                            if (!rename($fullname, $unzippath . "/" . $backdir))
                            {
                                $result['success'] = false;
                                $result['errormsg'] = "重命名目录{$fullname}失败";
                                break;
                            }

                        }
                    }
                }
            }

            closedir($dh);
        }
        return $result;
    }
}

