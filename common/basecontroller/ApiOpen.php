<?php
namespace basecontroller;

use core\Code;
use core\Response;
use jhelper\JCommon;
use jhelper\JHash;
use vendor\jeen\JLog;
use Yaf\Controller_Abstract;
use Yaf\Dispatcher;

class ApiOpen extends Controller_Abstract
{
    public $_m;
    public $_c;
    public $_a;
    public $_bodyId;

    public function init()
    {
        Response::getInstance();
        JLog::debug('ApiOpen init start', [], 'requestLog');
        $this->_m = $this->getRequest()->getModuleName();
        $this->_c = $this->getRequest()->getControllerName();
        $this->_a = $this->getRequest()->getActionName();
        $this->_bodyId = strtolower($this->_m . '_' . $this->_c . '_' . $this->_a);

        if ($this->getRequest()->isPost()) {
            JLog::debug('ApiOpen init is post:' . $this->_bodyId, [], 'requestLog');
            if (strtolower($this->_m) == 'wx' || JHash::checkCsrf()) {
                Dispatcher::getInstance()->autoRender(false);
            } else {
                $this->returnJson('','expire request,please check',Code::ExpireRequest);
                exit($this->getResponse()->getBody());
            }
        } else {
            JLog::debug('ApiOpen init not post:' . $this->_bodyId, [], 'requestLog');
            setrawcookie('jeen_auth', JHash::getCsrf(), time() + 3600, '/');
        }

        //... some other security logic




    }

    /**
     * @param string|mixed $json
     * @param bool $handle
     * @return array|string
     */
    public function getJson($json = '',bool $handle = true)
    {
        $json = $json ?: file_get_contents("php://input");
        if (!$handle) {
            return $json;
        }
        return JCommon::handleJsonParams($json);
    }

    /**
     * @param mixed $data
     * @param string $msg
     * @param int $code
     */
    protected function returnJson($data = null,string $msg = null,int $code = null)
    {
        try {
            header("Content-type:application/json;charset=utf-8");
        } catch (\Exception $e) {
            //...
        }
        $response = Response::getInstance();
        if (!is_null($data)) {
            $response->setData($data);
        }
        if (!is_null($msg)) {
            $response->setMsgContent($msg);
        }
        if (!is_null($code)) {
            $response->setCode($code);
        }
        $this->getResponse()->setBody(json_encode($response->getReply()));
    }

    /**
     * @param string $xml
     * @return mixed
     */
    public function getXml(string $xml = '')
    {
        $xmlStr = $xml ? : file_get_contents("php://input");
        return JCommon::xmlToArr($xmlStr);
    }

    /**
     * @param mixed $data
     * @param string $msg
     * @param int $code
     */
    protected function returnXml($data = null,string $msg = null,int $code = null)
    {
        try {
            header("Content-type:text/xml;charset=utf-8");
        } catch (\Exception $e) {
            //...
        }
        $response = Response::getInstance();
        if (!is_null($data)) {
            $response->setData($data);
        }
        if (!is_null($msg)) {
            $response->setMsgContent($msg);
        }
        if (!is_null($code)) {
            $response->setCode($code);
        }
        $xmlStr = JCommon::arrToXml($response->getReply());
        $this->getResponse()->setBody($xmlStr);
    }

    public function redirect($url)
    {
        $baseUri = JCommon::getBaseUrl();
        $url = str_replace('//', '/', $baseUri . $url);
        parent::redirect($url);
    }

}