<?php
/* Bootstrap for php unit test */
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('PS') or define('PS', PATH_SEPARATOR);
Yaf\Loader::import(APP_PATH . '/common/config/Init.php');  //类自动加载


class Bootstrap extends \Yaf\Bootstrap_Abstract
{
    public function _initConfig()
    {
        session_save_path(APP_PATH . '/var/session');
        \vendor\jeen\JLog::setLogger('tests');
        \Yaf\Registry::set('config', \Yaf\Application::app()->getConfig());
        Yaf\Dispatcher::getInstance()->autoRender(false); // 关闭模板自动渲染
    }

    public function _initPlugin(\Yaf\Dispatcher $dispatcher)
    {
//		$dispatcher->registerPlugin(new UriRewritePlugin());
//        $dispatcher->registerPlugin(new ThemePlugin());
//        $dispatcher->registerPlugin(new RBACPlugin());
    }

    public function _initRoute(\Yaf\Dispatcher $dispatcher)
    {
        $router = $dispatcher->getRouter();
        $supervar = new \Yaf\Route\Supervar('r');
        $router->addRoute('supervar',$supervar);
//        Jeen::show($router->getRoutes());
//        在这里注册自己的路由协议,默认使用简单路由
    }

    public function _initView(\Yaf\Dispatcher $dispatcher)
    {
        //在这里注册自己的view控制器，例如smarty,firekylin
    }

    public function _initApp()
    {
        //在这里进行一些其他的初始化操作
    }

}
