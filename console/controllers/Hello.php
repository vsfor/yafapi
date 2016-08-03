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
		$curl->setParams([
			'timeStamp' => time() - 1600,
		]);
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
		$yar->addTask('test/index/test',['b'=>1]);
		$yar->addTask('test/index/test',['b'=>2]);
		$yar->addTask('test/index/test',['b'=>3]);
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
		Jeen::echoln(json_encode($phpArr, JSON_UNESCAPED_UNICODE));
		$phpgz = \jhelper\JCommon::jZip($phpArr);
		Jeen::echoln($phpgz);
		Jeen::echoln(\jhelper\JCommon::jUnzip($phpgz));
		
		$jsgz = 'eJw9jWEOwjAIRk/zfq6ZdB3tz7HiNYxOvf8RVjRZwvsSAg9QQ+Q5IG+RvtIKNiM2IXfKHuWKGXWJDdlHvtJxOUd6I3MafEK3G7WNBi9snabf6UHul4ovWI2R2O/dStX/sYz2E5gdHgA=';
		$jsUngz = \jhelper\JCommon::jUnzip($jsgz);
		Jeen::echoln($jsUngz);
		
		$javagz = 'eJw9jVEOwjAMQ0/zPleNdF3az4WWa0xjwP2PQALSpNiSk9hGDZHDQd6Cx0or2IzYRLkjj2CfoZhRl3gS38sznZftTC9kTo53JNiN2lwwClun6Wfayf2yMhasxkns17hS9R+W0f4F8kYenQ==';
		$javaUngz = \jhelper\JCommon::jUnzip($javagz);
		Jeen::echoln($javaUngz);
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
	
	public function mongoAction()
	{

		try {
//			$mongo = new \MongoClient("mongodb://localhost:27017");

			$mongo = new \MongoDB\Driver\Manager("mongodb://localhost:27017");
		} catch(\Exception $e) {
			exit("MongoDB Connect Failed");
		}

		//  MongoClient  写法
//		$dbs = $mongo->listDBs();
//		Jeen::echoln($dbs);
//
//		$db = $mongo->selectDB('louli');
//		$collection = $db->selectCollection('base_community');
//
//		$r = $collection->findOne();
//		Jeen::jshow($r);

		// MongoDb\Driver 写法 
		$result = $mongo->executeQuery(
//			'db.collection', 
			'louli.base_community', 
			(new MongoDB\Driver\Query(['provincecode'=>['$in'=>[32,11]]], ['limit'=>3])),
			new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_PRIMARY_PREFERRED));
		// 返回的$result是一个对象，需要手动转换成数组。 
		Jeen::echoln($result->toArray());
	}
	
	public function mdbAction()
	{
		$tm = \models\mongo\TBTestA::getInstance();
//		$tm->delete();
//		for ($i=1; $i<11; $i++) {
//			$tm->insert(['a'=>$i,'b'=>$i%5]);
//		}  
		$r = $tm->one(); 
		Jeen::echoln($r); 
	}
	
}
