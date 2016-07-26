<?php
namespace vendor\jeen;
use core\JObject;

if (!function_exists('receiveJapi')) {
    function receiveJapi($retval, $callinfo) {
        JLog::debug('retval:'.json_encode($retval).'|callinfo:'.json_encode($callinfo), [], 'japi/callback');
    }
}

/**
 * Yar RPC api request manage component
 * 可通过缩短 yar.timeout 的配置值  实现伪异步操作
 * Class JApi
 * @package vendor\jeen
 */
class JApi extends JObject
{
    //-覆盖-单例模式Start
    private static $instance;

    /**
     * @param string $apiUrl
     * @return JApi
     */
    public static function getInstance($apiUrl=''){
        $unid = md5($apiUrl);
        if(empty(self::$instance[$unid])){
            self::$instance[$unid] = new self($apiUrl);
        }
        return self::$instance[$unid];
    }
    private function __construct($apiUrl) {
        if($apiUrl) $this->apiUrl = $apiUrl;
    }
    public function __clone() { throw new \Exception('Clone is not allowed !'); }
    //-覆盖-单例模式End

    private $apiUrl="http://apiyar.local.com";
    private $asynTask=[];

    /**
     * 同步调用 即时等待返回
     * @param string $route
     * @param mixed $params
     * @param string $method
     * @return null|string
     */
    public function call($route,$params,$method='post')
    {
        $task = [
            'r' => $route,
            'p' => $params,
            'm' => $method,
            't' => time()
        ];
        JLog::debug('syn task Info:'.json_encode($task),[],'japi/debug');
        $client = new \Yar_Client($this->apiUrl);
        $result = $client->handle($task);
        $result = isset($result) ? $result : null;
        JLog::debug('syn task result:'.json_encode($result),[],'japi/debug');
        return $result;
    }

    /**
     * 添加并发任务
     * @param string $route
     * @param array|mixed $params
     * @param string $method
     * @return mixed
     */
    public function addTask($route,$params,$method='post')
    {
        $task = [
            'r' => $route,
            'p' => $params,
            'm' => $method,
            't' => time()
        ];
        JLog::debug('add asyn task Info:'.json_encode($task),[],'japi/debug');
        $this->asynTask[] = $task;
        return self::$instance;
    }

    /**
     * 提交并发任务
     * @return bool
     */
    public function send()
    {
        try {
            JLog::debug(count($this->asynTask).'s asyn task send by user',[],'japi/debug');
            foreach($this->asynTask as $task) {
                \Yar_Concurrent_Client::call($this->apiUrl,'handle',$task, 'receiveJapi');
            }
            ob_start();
            \Yar_Concurrent_Client::loop();
            $result = ob_get_clean();
            JLog::debug('asyn task by user response:'.json_encode($result),[],'japi/debug');
            $this->asynTask = [];
            return true;
        } catch (\Exception $e) {
            JLog::error('asyn task execute by user error:'
                . json_encode($this->asynTask) . PHP_EOL
                . 'Exception:'.$e->getMessage() . PHP_EOL
                . $e->getTraceAsString(), [], 'japi/debug');
            return false;
        }
    }

    /**
     * 析构方法, 实现伪异步
     */
    public function __destruct()
    {
        try {
            JLog::debug(count($this->asynTask).' asyn task auto send',[],'japi/debug');
            foreach($this->asynTask as $task) {
                \Yar_Concurrent_Client::call($this->apiUrl,'handle',$task, 'receiveJapi');
            }
            ob_start();
            \Yar_Concurrent_Client::loop();
            $result = ob_get_clean();
            JLog::debug('asyn task auto response:'.json_encode($result),[],'japi/debug');
            $this->asynTask = [];
        } catch (\Exception $e) {
            JLog::error('asyn task auto execute error:'
                . json_encode($this->asynTask) . PHP_EOL
                . 'Exception:'.$e->getMessage() . PHP_EOL
                . $e->getTraceAsString(), [], 'japi/debug');
        }
    }
}
