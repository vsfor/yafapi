<?php
namespace tests;

class TestCase extends \PHPUnit_Framework_TestCase
{

    protected $application = NULL;

    function __construct()
    {
        $this->application = $this->getApplication();
        parent::__construct();
    }

    public function getApplication()
    {
        $appliction = \Yaf\Registry::get('application');
        if(!$appliction)
        {
            $this->setApplication();
        }
        return \Yaf\Registry::get('application');
    }

    public function setApplication()
    {
        $application = new \Yaf\Application(APP_PATH . "/common/config/testcase.ini");
        $application->bootstrap();
        \Yaf\Registry::set('application', $application);
    }
}