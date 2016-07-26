<?php

class HelloController extends \basecontroller\Console
{
	public function indexAction()
	{
        Jeen::echoln(__FILE__.__LINE__);
        \vendor\jeen\JLog::debug('abc');
	}

	public function apiopenAction()
	{
		$curl = new \jhelper\JCurl();
		$curl->url = 'http://yafapi.local.com';
		$curl->setUserAgent('abc')
			->setHeader('headeraaa','ddd')
			->setHeader('headerddd','eee')
			->setCookie('cookieaaa','bbb')
			->setCookie('cookiebbb','ccc')
			->setMethod('post')->setParamType('json');
		$curl->setCookie('jeen_auth', \jhelper\JHash::getCsrf()); // 简单的跨站检测
		$curl->call();
		Jeen::echoln($curl->getRequestHeader());
		echo '---';
		Jeen::echoln($curl->getRequestContent());
		echo '---';
		Jeen::echoln($curl->getErrInfo());
		echo '---';
		Jeen::echoln($curl->getResponseCode());
		echo '---';
		Jeen::echoln($curl->getResponseHeader());
		echo '---';
		Jeen::echoln($curl->getResponseContent());
	}

	public function apiyarAction()
	{
		$yar = \vendor\jeen\JApi::getInstance('http://yarapi.local.com');
		$yar->addTask('test/index/test',['b'=>4]);
		$t = $yar->call('test/index/test', ['a'=>2]);
		Jeen::echoln($t);
	}

	
}
