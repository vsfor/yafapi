<?php
namespace core;
class JMongo extends JObject
{
    //-覆盖-单例模式Start
    private static $instance;

    /**
     * @param string $configName
     * @return \MongoClient
     */
    public static function getInstance($configName='common'){
        if(empty(self::$instance[$configName])){
            new self($configName);
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
            $this->configName = $configName;
            $config = \Yii::$app->params['mongodb'][$configName];
            $options = [];
            if($config['connect']) $options['connect'] = $config['connect'];
            if($config['timeout']) $options['timeout'] = $config['timeout'];
            if($config['username']) $options['username'] = $config['username'];
            if($config['password']) $options['password'] = $config['password'];
            self::$instance[$configName] = new \MongoClient("mongodb://{$config['host']}:{$config['port']}",$options);
        } catch(\Exception $e) {
            JLog::log('mongoDB connect failed:'.$configName,'mongodb');
            throw $e;
        }
    }
    public function __clone() { throw new \Exception('Clone is not allowed !'); }
    //-覆盖-单例模式End

    private $configName;

    public function __destruct()
    {
        try {
            if(self::$instance) self::$instance[$this->configName]->close(true);
        } catch (\Exception $e) {
            JLog::error('mongodb close error:'.$this->configName
                .'|'.$e->getMessage()
                .'|'.$e->getTraceAsString(),[],'jmongo');
        }
    }
}
