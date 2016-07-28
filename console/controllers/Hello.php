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

	public function jsphpgzAction()
	{
		$phpArr = [
			'a' => '数+-/\组',
			'b.c' => 'c.d . e汉 字f-_=',
			'中+文' => 3,
		];
		$phpgz = \jhelper\JCommon::jZip($phpArr);
		Jeen::echoln($phpgz);
		Jeen::echoln(\jhelper\JCommon::jUnzip($phpgz));
		
		$jsgz = 'eJw9jWEOwjAIRk/zfq6ZdB3tz7HiNYxOvf8RVjRZwvsSAg9QQ+Q5IG+RvtIKNiM2IXfKHuWKGXWJDdlHvtJxOUd6I3MafEK3G7WNBi9snabf6UHul4ovWI2R2O/dStX/sYz2E5gdHgA=';
		$jsUngz = \jhelper\JCommon::jUnzip($jsgz);
		Jeen::echoln($jsUngz);
		Jeen::echoln(\jhelper\JCommon::jZip($jsUngz));
	}
	
	public function dbAction()
	{
		$lib = \models\mysql\TBTestA::getInstance();
		Jeen::echoln($lib->getData());
	}
	
	public function cacheAction()
	{
		$r = \core\JRedis::getInstance();
		
		$key = 'api:rateTest'; 
		$r->lPush($key, 1); 
		Jeen::echoln(json_encode($r->lRange($key, 0, -1))); 
		Jeen::echoln($r->expire($key, 3)); 
		sleep(3);
		$r->lPush($key,2);
		Jeen::echoln(json_encode($r->lRange($key,0,-1)));
		Jeen::echoln($r->expire($key, 3));
		sleep(2);
		$r->lPush($key, 3);
		Jeen::echoln($r->expire($key, 3));
		sleep(2);
		Jeen::echoln(json_encode($r->lRange($key,0,-1)));
	}
	
}
