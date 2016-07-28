<?php
Yaf\Loader::import(APP_PATH . '/common/config/Init.php');  //类自动加载

class Bootstrap extends \Yaf\Bootstrap_Abstract
{
	public function _initConfig()
	{
        session_save_path(APP_PATH . '/var/session');
//        session_start();
		\Yaf\Registry::set('config', \Yaf\Application::app()->getConfig());
		Yaf\Dispatcher::getInstance()->autoRender(false); // 关闭自动加载模板
//        Jeen::echoln('_initConfig');
	}

    public function _initRoute(\Yaf\Dispatcher $dispatcher)
    {
//        Jeen::echoln('_initRoute');
//        在这里注册自己的路由协议,默认使用简单路由

        $router = $dispatcher->getRouter();
        $supervar = new \Yaf\Route\Supervar('r');
        $router->addRoute('supervar',$supervar);
    }

    
}
