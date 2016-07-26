<?php

class SayhiController extends \basecontroller\Console
{
    public function morningAction()
    {
        Jeen::echoln('morning');
        Jeen::echoln( __FILE__ . ':' . __LINE__ );
        Jeen::echoln( $this->getRequest()->getParams() );
        global $argc,$argv;
        Jeen::echoln($argc);
        Jeen::echoln($argv);
    }
}