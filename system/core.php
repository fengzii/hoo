<?php

/**
 * 初始化文件
 * @author scan<232832288@qq.com>
 * @version 1.0 2008-9-10
 */



//框架版本
define('VERSION', '1.0');

//定义系统路径
if (!defined('SYS_PATH')) define('SYS_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('BASE_PATH', dirname(SYS_PATH).DIRECTORY_SEPARATOR);
define('CORE_PATH', SYS_PATH.'core'.DIRECTORY_SEPARATOR);
define('APP_PATH', BASE_PATH.APP_NAME.DIRECTORY_SEPARATOR);
define('LIB_PATH', SYS_PATH.'lib'.DIRECTORY_SEPARATOR);
define('PLU_PATH', SYS_PATH.'plugins'.DIRECTORY_SEPARATOR);
define('MOD_PATH', SYS_PATH.'module'.DIRECTORY_SEPARATOR);
define('CONF_PATH', SYS_PATH.'config'.DIRECTORY_SEPARATOR);
define('BUILD_PATH', SYS_PATH.'.build'.DIRECTORY_SEPARATOR);

define('TMP_PATH', BASE_PATH.'tmp'.DIRECTORY_SEPARATOR);

define('APP_LIB_PATH', APP_PATH.'lib'.DIRECTORY_SEPARATOR);
define('APP_PLU_PATH', APP_PATH.'plugins'.DIRECTORY_SEPARATOR);
define('APP_CONF_PATH', APP_PATH.'config'.DIRECTORY_SEPARATOR);
define('APP_BUILD_PATH', APP_PATH.'.build'.DIRECTORY_SEPARATOR);

require(SYS_PATH . '/.build/runtime.php');
?>