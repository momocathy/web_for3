<?php defined('SYSPATH') or die('No direct script access.');

// -- Environment setup --------------------------------------------------------

// Load the core Kohana class
require SYSPATH . 'classes/kohana/core' . EXT;

if (is_file(APPPATH . 'classes/kohana' . EXT))
{
    // Application extends the core
    require APPPATH . 'classes/kohana' . EXT;
}
else
{
    // Load empty core extension
    require SYSPATH . 'classes/kohana' . EXT;
}

/**
 * Set the default time zone.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/timezones
 */
//date_default_timezone_set('America/Chicago');
date_default_timezone_set('Asia/Shanghai');

/**
 * Set the default locale.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/function.setlocale
 */
setlocale(LC_ALL, 'en_US.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @link http://kohanaframework.org/guide/using.autoloading
 * @link http://www.php.net/manual/function.spl-autoload-register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Optionally, you can enable a compatibility auto-loader for use with
 * older modules that have not been updated for PSR-0.
 *
 * It is recommended to not enable this unless absolutely necessary.
 */
//spl_autoload_register(array('Kohana', 'auto_load_lowercase'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @link http://www.php.net/manual/function.spl-autoload-call
 * @link http://www.php.net/manual/var.configuration#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

/**
 * Set the mb_substitute_character to "none"
 *
 * @link http://www.php.net/manual/function.mb-substitute-character.php
 */
mb_substitute_character('none');

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('ch'); //读取语言包

if (isset($_SERVER['SERVER_PROTOCOL']))
{
    // Replace the default protocol.
    HTTP::$protocol = $_SERVER['SERVER_PROTOCOL'];
}

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
if (isset($_SERVER['KOHANA_ENV']))
{
    Kohana::$environment = constant('Kohana::' . strtoupper($_SERVER['KOHANA_ENV']));
}
Cookie::$salt = 'stourwebcms';
// 设置cookie 多久过期
Cookie::$expiration = 86400;
/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - integer  cache_life  lifetime, in seconds, of items cached              60
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 * - boolean  expose      set the X-Powered-By header                        FALSE
 */
//定义目录
$cfg_phone_cmspath = '';
$cfg_mobile_config = require(dirname(DOCROOT) . '/data/mobile.php');
//跳转手机站
if (!empty($cfg_mobile_config['domain']['mobile']) && $cfg_mobile_config['domain']['mobile'] != $cfg_mobile_config['domain']['main'])
{
    $mainHost=parse_url($cfg_mobile_config['domain']['main'], PHP_URL_HOST);
    if($_SERVER['HTTP_HOST'] == $mainHost){
        header('Location:' . $cfg_mobile_config['domain']['mobile']);
        exit;
    }
}
//手机版域名未设置或与主站域名相同
if (empty($cfg_mobile_config['domain']['mobile']) || $cfg_mobile_config['domain']['mobile'] == $cfg_mobile_config['domain']['main'] || $_SERVER['HTTP_HOST'] != parse_url($cfg_mobile_config['domain']['mobile'], PHP_URL_HOST))
{
    $cfg_phone_cmspath = '/phone';
}
//kohana 初始化
Kohana::init(array(
    'base_url' => $cfg_phone_cmspath . '/',
    'index_file' => ''
));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH . 'logs'));

Kohana::$log_errors = false;//关闭日志记录

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
    // 'auth'       => MODPATH.'auth',       // Basic authentication
    // 'cache'      => MODPATH.'cache',      // Caching with multiple backends
    // 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
    'database' => MODPATH . 'database',   // Database access
    'image' => MODPATH . 'image',      // Image manipulation
    // 'minion'     => MODPATH.'minion',     // CLI Tasks
    'orm' => MODPATH . 'orm',       // Object Relationship Mapping
    'pagination' => MODPATH . 'pagination', //分页
    'captcha' => MODPATH . 'captcha'//验证码类

    // 'unittest'   => MODPATH.'unittest',   // Unit testing
    // 'userguide'  => MODPATH.'userguide' // User guide and API documentation
));


/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */

require(APPPATH . 'route.php');
$cfg_basehost = 'http://' . $_SERVER['HTTP_HOST']; //网站域名
$cfg_default_templet = '/default/';
$cfg_public_url = $cfg_phone_cmspath . '/public/';
//手机版系统信息写入缓存
extract(Common::cache_config());
//子站判断与处理
Common::cache_web_list();
//主站点webid
$sys_webid = 0;
//主站点prefix
$sys_prefix = Common::get_main_prefix();
//子站检测
if (file_exists(APPPATH . '/cache/weblist.php'))
{
    require_once APPPATH . '/cache/weblist.php';
    $url = $_SERVER['HTTP_HOST'];//当前域名
    $uarr = explode('.', $url);
    $prefix = $uarr[0]; //当前域名前辍
    if (array_key_exists($prefix, $weblist))
    {
        //重置当前webid,prefix
        $sys_webid = $weblist[$prefix]['webid'];
        $sys_prefix = $prefix;
    }
}










