<?php
namespace basecontroller;

use core\Response;
use jhelper\JCommon;
use vendor\jeen\JLog;
use Yaf\Controller_Abstract;

class ApiYar extends Controller_Abstract
{
    public $_m;
    public $_c;
    public $_a;
    public $_bodyId;

    public function init()
    {
        Response::getInstance();
        JLog::debug("ApiYar init start",[],'japi/debug');
        $this->_m = $this->getRequest()->getModuleName();
        $this->_c = $this->getRequest()->getControllerName();
        $this->_a = $this->getRequest()->getActionName();
        $this->_bodyId = strtolower($this->_m . '_' . $this->_c . '_' . $this->_a);

        //... some other security logic


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

}