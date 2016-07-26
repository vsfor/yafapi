<?php
/**
 * Created by PhpStorm.
 * User: jeen
 * Date: 15-12-19
 * Time: 16:53
 */
use jhelper\JT;

class JTTest extends \tests\TestCase {
    public function testJTA()
    {
        Jeen::echoln('this is just a test echo');
        $a = JT::getInstance();
        $this->assertEquals(2,$a->a);
    }
}
 