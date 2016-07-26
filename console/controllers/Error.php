<?php
class ErrorController extends \Yaf\Controller_Abstract
{
	public function errorAction()
	{
        echo \Yaf\Dispatcher::getInstance()->getRequest()->getException();
        return false;
	}
    public function indexAction()
    {
        echo 'error #9001';
        return false;
    }
}
