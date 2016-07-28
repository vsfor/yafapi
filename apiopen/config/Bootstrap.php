<?php
Yaf\Loader::import(APP_PATH . '/common/config/Init.php');  //类自动加载

class Bootstrap extends \Yaf\Bootstrap_Abstract
{
	public function _initConfig()
	{
        \vendor\jeen\JLog::setLogger('apiopen');
        \vendor\jeen\JLog::debug('Bootstrap _initConfig', [], 'requestLog');

        session_save_path(APP_PATH . '/var/session');
//        session_start();

		\Yaf\Registry::set('config', \Yaf\Application::app()->getConfig());
        \Yaf\Registry::set('apiConfig',require(__DIR__."/api_".J_ENV.".php"));
        \Yaf\Registry::set('requestConfig',require(__DIR__."/request_".J_ENV.".php"));
        Yaf\Dispatcher::getInstance()->disableView(); //关闭自动加载模板
//		Yaf\Dispatcher::getInstance()->autoRender(false); // 关闭自动加载模板
	}

}
