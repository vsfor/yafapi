<?php
class IndexController extends \basecontroller\ApiOpen
{
    public function testAction()
    {
        $t = \Yaf\Registry::get('apiConfig');
        Jeen::echoln($t);
        $this->returnJson(['token'=>md5('hi')]);
    }
}