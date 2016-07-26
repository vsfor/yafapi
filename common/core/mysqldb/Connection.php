<?php
namespace core\mysqldb;
use core\JObject;

class Connection extends JObject
{
    private static $instance = [];

    private function __construct($configName, $configInfo)
    {
        if(isset(self::$instance[$configName]) && self::$instance[$configName]) {
            return self::$instance[$configName];
        }
        $options = array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES ".$configInfo->charset,
        );
        $conn = new \PDO($configInfo->dsn, $configInfo->username, $configInfo->password, $options);
        $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);
        $conn->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        self::$instance[$configName] = $conn;
        return $conn;
    }

    /**
     * @param string $configName
     * @return \PDO
     */
    public static function getInstance($configName='common')
    {
        if(isset(self::$instance[$configName]) && self::$instance[$configName]) {
            return self::$instance[$configName];
        }
        $config = \Yaf\Registry::get('config');
        $dbInfo = $config['data']['mysql'][$configName];

        new self($configName, $dbInfo);
        return self::$instance[$configName];
    }
}