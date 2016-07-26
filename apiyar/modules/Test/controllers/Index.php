<?php
class IndexController extends \basecontroller\ApiYar
{
    public function testAction()
    {
        $this->returnJson(['token'=>md5('hi')]);
    }
}