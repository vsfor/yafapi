<?php
namespace jhelper;
use core\JObject;

/**
 * 这是一个测试Helper类
 * Class JT
 * @package helper
 */
class JT extends JObject
{
    //-覆盖-单例模式Start
    private static $instance;
    public static function getInstance(){
        if(empty(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }
    private function __construct() { }
    public function __clone() { throw new \Exception('Clone is not allowed !'); }
    //-覆盖-单例模式End

    public $a = 2;

    /**
     * 这是方法说明
     * @param string $a
     * @param int $b
     * @return string
     */
    public function abc(string $a,int $b)
    {
        return $a . strval($b);
    }

}