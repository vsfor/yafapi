<?php
namespace core;
/**
 * 单例模式基类
 * Class JObject
 * @package core
 */
class JObject
{
    public static function className()
    {
        return get_called_class();
    }
    //--单例模式Start
    private static $instance;
    public static function getInstance(){
        $class = get_called_class();
        if(empty(self::$instance[$class])){
            self::$instance[$class] = new $class();
        }
        return self::$instance[$class];
    }
    private function __construct() { }
    public function __clone() { throw new \Exception('Clone is not allowed !'); }
    //--单例模式End



}