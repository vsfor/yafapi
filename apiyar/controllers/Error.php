<?php
class ErrorController extends \basecontroller\ApiYar
{
	public function errorAction()
	{
        \Yaf\Application::app()->getDispatcher()->autoRender(false);
        if(defined('J_DEBUG') && J_DEBUG) {
            $e = \Yaf\Dispatcher::getInstance()->getRequest()->getException();
            Jeen::show($e->getFile().':'.$e->getLine().':'.$e->getCode());
            Jeen::show($e->getMessage());
            Jeen::show($e->getTraceAsString());
            Jeen::show($e->getTrace());
            Jeen::show($e);
        } else {
            exit('500');
        }
	}
    public function indexAction()
    {
        echo 'error #9001';
        return false;
    }
}
?>