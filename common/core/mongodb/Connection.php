<?php
namespace core\mongodb;
use core\JObject;

/**
 * 暂不推荐使用,  如有需要  建议参阅相关文档 进行封装后使用
 * 参考博文 http://my.oschina.net/jsk/blog/644287
 * MongoDB PHP Library https://github.com/mongodb/mongo-php-library
 * PHP Manual http://php.net/manual/en/set.mongodb.php
 * Class Connection
 * @package core
 */
class Connection extends JObject
{
    //-覆盖-单例模式Start
    private static $instance;

    /**
     * @param string $configName
     * @return \MongoDB\Driver\Manager
     * @throws \Exception
     */
    public static function getInstance($configName='common'){
        if(!empty(self::$instance[$configName])){
            return self::$instance[$configName];
        }
        $config = \Yaf\Registry::get('config')->data;
        if(!isset($config->mongo->$configName)) {
            throw new \Exception("mongo config not exists");
        }
        new self($configName, $config->mongo->$configName);
        return self::$instance[$configName];
    }

    private function __construct($configName, $configInfo) {
        try {
            $options = [];
            if($configInfo['connect']) $options['connect'] = boolval($configInfo['connect']);
            if($configInfo['timeout']) $options['timeout'] = intval($configInfo['timeout']);
            if($configInfo['username']) $options['username'] = $configInfo['username'];
            if($configInfo['password']) $options['password'] = $configInfo['password'];
            self::$instance[$configName] = new \MongoDB\Driver\Manager("mongodb://{$configInfo['host']}:{$configInfo['port']}",$options);
        } catch(\Exception $e) {
            throw new \Exception("mongodb connect failed");
        }
    }
    public function __clone() { throw new \Exception('Clone is not allowed !'); }
    //-覆盖-单例模式End
 
}
