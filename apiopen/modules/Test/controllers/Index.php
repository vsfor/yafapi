<?php
class IndexController extends \basecontroller\ApiOpen
{
    public function testAction()
    {
        $this->returnJson(['token'=>md5('hi')]);
    }
}