<?php
namespace basecontroller;

use core\FilterApi;
use core\Code;
use core\FilterRequest;
use core\Response;
use jhelper\JCommon;
use jhelper\JHash;
use vendor\jeen\JLog;
use vendor\yiihelpers\ArrayHelper;
use Yaf\Controller_Abstract;

class ApiOpen extends Controller_Abstract
{
    public $_m;
    public $_c;
    public $_a;
    public $_bodyId;

    public function init()
    {
        JLog::debug('ApiOpen init start', [], 'requestLog');
        Response::getInstance();
        $this->_m = $this->getRequest()->getModuleName();
        $this->_c = $this->getRequest()->getControllerName();
        $this->_a = $this->getRequest()->getActionName();
        $this->_bodyId = strtolower($this->_m . '_' . $this->_c . '_' . $this->_a);

        $req_body = file_get_contents("php://input");
        $req_logInfo = "ApiOpen Request Information:"
            ."\n\t Cookie: ".json_encode($_COOKIE)
            ."\n\t Server: ".json_encode($_SERVER)
            ."\n\t Get: ".json_encode($_GET)
            ."\n\t Post: ".json_encode($_POST)
            ."\n\t Body: ".json_encode($req_body);
        \vendor\jeen\JLog::debug($req_logInfo, [], 'requestLog');

        $params = $this->getRequest()->getParams() ? : [];
        $get = JCommon::handleJsonParams($_GET);
        $post = JCommon::handleJsonParams($_POST);
        $body = $req_body ? JCommon::handleJsonParams($req_body) : [];
        if ($body && !is_array($body)) {
            $body = [$body];
        }
        $requestParams = ArrayHelper::merge(//参数处理合并
            $params,
            $get,
            $post,
            $body
        );

        /* @notice 必要 用于请求方式校验 */
//        $requestParams['__method'] = strtolower($this->getRequest()->getMethod());

        JLog::debug('ApiOpen '.$this->_bodyId.' Final Params:'.json_encode($requestParams), [], 'requestLog');

        //request filter
        $checkCode = FilterRequest::check($requestParams);
        switch ($checkCode) {
            case FilterRequest::checkPass:
                break;
            case FilterRequest::timeError:
                $this->returnJson('', 'deny 1', Code::InvalidRequest);
                break;
            case FilterRequest::ipDeny:
                $this->returnJson('', 'deny 2', Code::InvalidRequest);
                break;
            case FilterRequest::userAgentDeny:
                $this->returnJson('', 'deny 3', Code::InvalidRequest);
                break;
            case FilterRequest::userIdDeny:
                $this->returnJson('', 'deny 4', Code::InvalidRequest);
                break;
            default:
                $this->returnJson('', 'unknown deny', Code::InvalidRequest);
                break;
        }
        if ($checkCode != FilterRequest::checkPass) {
            exit($this->getResponse()->getBody());
        }

        //api config filter
        $checkCode = FilterApi::check($this->_bodyId, $requestParams);
        switch ($checkCode) {
            case FilterApi::checkPass:
                break;
            case FilterApi::apiNotFound:
                $this->returnJson('', $this->_bodyId . ',not found', Code::InvalidRequest);
                break;
            case FilterApi::apiClosed:
                $this->returnJson('', $this->_bodyId . ',close', Code::InvalidRequest);
                break;
            case FilterApi::dateCheckFailed:
                $this->returnJson('', $this->_bodyId . ',out date', Code::InvalidRequest);
                break;
            case FilterApi::levelLimited:
                $this->returnJson('', $this->_bodyId . ',level limit', Code::InvalidRequest);
                break;
            case FilterApi::requestMethodError:
                $this->returnJson('', $this->_bodyId . ',__method error', Code::InvalidRequest);
                break;
            case FilterApi::versionCheckFailed:
                $this->returnJson('', $this->_bodyId . ',version expire', Code::InvalidRequest);
                break;
            case FilterApi::rateLimited:
                $this->returnJson('', $this->_bodyId . ',too fast', Code::InvalidRequest);
                break;
            default:
                $this->returnJson('', $this->_bodyId . ',unknown', Code::InvalidRequest);
                break;
        }
        if ($checkCode != FilterApi::checkPass) {
            exit($this->getResponse()->getBody());
        }

        //... some other security logic

        // eg: csrf check
//        if ($this->getRequest()->isPost()) {
//            if (strtolower($this->_m) != 'wx' && !JHash::checkCsrf()) {
//                $this->returnJson('','expire request,please check',Code::ExpireRequest);
//                exit($this->getResponse()->getBody());
//            }
//        } else {
//            setrawcookie('jeen_auth', JHash::getCsrf(), time() + 3600, '/');
//        }

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