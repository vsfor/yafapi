<?php
Yaf\Loader::import(APP_PATH . '/common/config/Init.php');  //类自动加载

class Bootstrap extends \Yaf\Bootstrap_Abstract
{
	public function _initConfig()
	{
//        Jeen::echoln('_initConfig');
        session_save_path(APP_PATH . '/var/session');
        \vendor\jeen\JLog::setLogger('apiopen');
        \vendor\jeen\JLog::debug('Bootstrap _initConfig', [], 'requestLog');
		\Yaf\Registry::set('config', \Yaf\Application::app()->getConfig());
		Yaf\Dispatcher::getInstance()->autoRender(false); // 关闭自动加载模板
	}

	public function _initPlugin(\Yaf\Dispatcher $dispatcher)
	{
//        Jeen::echoln('_initPlugin');
	}

    public function _initRoute(\Yaf\Dispatcher $dispatcher)
    {
//        Jeen::echoln('_initRoute');

//        \vendor\jeen\JLog::debug('Bootstrap _initRoute', [], 'requestLog');
//        $router = $dispatcher->getRouter();
//        $supervar = new \Yaf\Route\Supervar('r');
//        $router->addRoute('supervar',$supervar);
//        Jeen::show($router->getRoutes());
//        在这里注册自己的路由协议,默认使用简单路由
    }

    public function _initView(\Yaf\Dispatcher $dispatcher)
    {
//        Jeen::echoln('_initView');
        \vendor\jeen\JLog::debug('Bootstrap _initView', [], 'requestLog');
        //在这里注册自己的view控制器，例如smarty,firekylin
    }

    public function _initApp()
    {
//        Jeen::echoln('_initApp');
        \vendor\jeen\JLog::debug('Bootstrap _initApp', [], 'requestLog');
        session_start();
    }

}
