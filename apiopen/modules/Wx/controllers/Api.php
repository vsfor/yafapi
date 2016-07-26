<?php
use vendor\yiihelpers\Json;
class ApiController extends \basecontroller\ApiOpen
{ 
    public function msgAction()
    {
        \vendor\jeen\JLog::debug('Wx -> ApiController -> msgAction start', [], 'requestLog');
        if (\vendor\jeen\wechat\Mp::getInstance()->checkSignature()) {
            if (isset($_GET['echostr'])) {
                echo $_GET['echostr'];
                exit();
            }
            \vendor\jeen\JLog::debug('Wx -> ApiController -> msgAction sign checked', [], 'requestLog');
            try {
                $body = $this->getJson('', false);
                \vendor\jeen\wechat\Mp::getInstance()->responseMsg($body);
            } catch (\Exception $e) {
                \vendor\jeen\JLog::debug('Exception:'.$e->getMessage().PHP_EOL.$e->getTraceAsString(), [], 'requestLog');
            }
        }
        \vendor\jeen\JLog::debug('Wx -> ApiController -> msgAction end', [], 'requestLog');
        return "";
    }

    public function menuAction()
    { 
        \vendor\jeen\JLog::debug(Json::encode($_GET).PHP_EOL.Json::encode($_POST).PHP_EOL.Json::encode($this->getJson('',false)), [], 'menulog');
        echo 'aaa';
        exit();
    }
    
}