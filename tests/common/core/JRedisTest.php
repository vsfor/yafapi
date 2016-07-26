<?php
/**
 * Created by PhpStorm.
 * User: jeen
 * Date: 15-12-19
 * Time: 16:53
 */
use jhelper\JT;

class JRedisTest extends \tests\TestCase {
    public function testJRedis()
    {
        Jeen::echoln('this is just a test echo');
        $a = \core\JRedis::getInstance();
        $a->set('phpunitRedisTest', 'a');
        $this->assertEquals('a', $a->get('phpunitRedisTest'));
    }
}