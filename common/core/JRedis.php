<?php
namespace core;

class JRedis extends JObject
{
    //-覆盖-单例模式Start
    private static $instance = [];

    /**
     * @param string $configName
     * @return \Redis
     * @throws \Exception
     */
    public static function getInstance($configName = 'common')
    {
        if(isset(self::$instance[$configName]) && self::$instance[$configName]) {
            $tr = self::$instance[$configName];
            if ('+PONG' == $tr->ping()) {
                return $tr;
            }
        }
        $config = \Yaf\Registry::get('config')->data;
        if(!isset($config->redis->$configName)) {
            throw new \Exception("redis config not exists");
        }
        new self($configName, $config->redis->$configName);
        return self::$instance[$configName];
    }

    private function __construct($configName, $configInfo)
    {
        $redis = new \Redis();
        $timeOut = isset($configInfo['timeout']) ? intval($configInfo['timeout']) : 3;
        $dbIndex = isset($configInfo['db']) ? intval($configInfo['db']) : 0;
        if ($redis->connect($configInfo['host'], $configInfo['port'], $timeOut)) {
            if(isset($configInfo['pass']) && $configInfo['pass']) {
                $redis->auth($configInfo['pass']);
            }
            $redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
            $redis->select($dbIndex);
            self::$instance[$configName] = $redis;
        } else {
            throw new \Exception("redis is down");
        }
        return true;


        //阿里云 redis 使用范例
        /*
        $host = '412c55b0c79711e4.m.cnbja.kvstore.aliyuncs.com';
        $port = 6379;
        $auth = '412c55b0c79711e4:REDISlouli2015';
        $timeOut = 2; //超时时间  N second(s)

        $redis = new \Redis();
        if ($redis->connect($host, $port, $timeOut)) {
            if($redis->auth($auth)) {
                $redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
            } else {
                throw new \Exception("Jedis Auth Failed");
            }
        } else {
            throw new \Exception("Jedis Connect Failed");
        }
        self::$instance[$config] = $redis;
        return true;
        */
    }

    public function __clone() { throw new \Exception('Clone is not allowed !'); }
    //-覆盖-单例模式End

}
