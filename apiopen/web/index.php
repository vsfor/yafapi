<?php
if(isset($_GET['_see']) && $_GET['_see'] == 'info') {
    phpinfo();exit();
}
define("APP_PATH", dirname(dirname(__DIR__)));
define("DS", DIRECTORY_SEPARATOR); /* 目录分隔符 */
define("PS", PATH_SEPARATOR); /* 路径分隔符 */
define("J_ENV", "local"); //项目运行环境划分
define("J_LEVEL", 1); //项目运行级别,  压力过大时,可调高此值  实现接口降级
define("J_DEBUG", false); //全局调试控制
if(J_DEBUG) {
    error_reporting(E_ALL);
} else {
    error_reporting(E_ERROR);
}

/* 基础运行环境检测 Start */
if (version_compare(phpversion(), '5.4.0', '<') === true) {
    die('PHP Version 5.4 or newer needed');
}
if (!extension_loaded('yaf')) {
    die('Yaf extension needed');
}
if (!ini_get('yaf.use_namespace')) {
    die('php.ini - [yaf] yaf.use_namespace=1 needed');
}
if (!ini_get('yaf.use_namespace') || !ini_get('yaf.use_spl_autoload')) {
    die('php.ini - [yaf] yaf.use_namespace=1 and yaf.use_spl_autoload=1 needed');
}
/* 环境检测 End  环境部署完成后  可移除上述代码 */

/* 虚拟空间未安装Yaf扩展 可开启上述 自动加载类 * /
if (!extension_loaded('yaf')) {
    define("APPLICATION_PATH", APP_PATH); // 指向入口文件的上一级
    include(APPLICATION_PATH . '/framework/loader.php');
}
/* yaf port end */
$global_start = microtime(true);
try {
    $app = new \Yaf\Application(APP_PATH . "/apiopen/config/" . J_ENV . ".ini");
    $app->bootstrap();
    $app->run();
} catch (\Exception $e) {
    \vendor\jeen\JLog::debug("Exception: \n\t ".$e->getMessage()."\n\t ".$e->getTraceAsString(), [], 'requestLog');
    
    if(J_DEBUG) {
        Jeen::echoln('==Exception Info Start============');
        Jeen::echoln($app->environ());
        Jeen::show($app->getConfig()->toArray());
        Jeen::echoln($e->getMessage());
        Jeen::show($e->getTrace());
        Jeen::echoln($e->getTraceAsString());
        Jeen::echoln('==Exception Info End============');
    } else {
        exit('500 '.$e->getMessage());
    }
}
$global_end = microtime(true);
$req_timeSpend = "Total Time Spend: \n\t $global_start -> $global_end  \n\t => ".(($global_end-$global_start) * 1000).'(ms)';
\vendor\jeen\JLog::debug($req_timeSpend, [], 'requestLog');