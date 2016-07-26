<?php
Yaf\Loader::import(APP_PATH . '/common/config/Init.php');  //类自动加载

class Bootstrap extends \Yaf\Bootstrap_Abstract
{
    public function _initConfig()
    {
//        Jeen::echoln(__FILE__.':'.__LINE__);
//        Jeen::echoln('_initConfig');
        Yaf\Dispatcher::getInstance()->autoRender(false); // 关闭自动加载模板
        Yaf\Registry::set('config', \Yaf\Application::app()->getConfig());
        \vendor\jeen\JLog::setLogger('console');
    }

    public function _initPlugin(\Yaf\Dispatcher $dispatcher)
    {
//        Jeen::echoln('_initPlugin');
    }

    public function _initRoute(\Yaf\Dispatcher $dispatcher)
    {
//        Jeen::echoln('_initRoute');
    }

    public function _initView(\Yaf\Dispatcher $dispatcher)
    {
//        Jeen::echoln('_initView');
    }

    public function _initApp()
    {
//        Jeen::echoln('_initApp');
    }

}
