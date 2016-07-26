<?php
namespace core;

use jhelper\JCommon;
use jhelper\JHash;

class Response
{
    /** @var  $instance Response */
    static private $instance;
    private $_start = 0;
    protected $reply = [
        'timeSpend' => 0, //执行时间 ms
        'msgLevel' => 1, //消息提示级别  0不提示  1弱提示   2强提示  其他不提示
        'msgTitle' => '', //描述标题
        'msgContent' => '', //描述内容
        'resultCode' => 1000, //状态码
        'resultData' => '', //返回数据主体
        'dataZipped' => 0, //是否压缩  0否  1是
        'responseToken' => '', //动态Token, 可用于 csrf 检测
    ];
    private function __construct() {
        $this->_start = isset($_SERVER['REQUEST_TIME_FLOAT']) ? $_SERVER['REQUEST_TIME_FLOAT'] : microtime(true);
        $this->reply['responseToken'] = JHash::getCsrf();
    }

    /**
     * @return Response
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * 获取响应内容  建议只在 最终返回时调用
     * @return array
     */
    public function getReply()
    {
        if (is_null($this->reply['msgContent'])) {
            $this->reply['msgContent'] = Code::getCodeDes($this->reply['resultCode']);
        }
        if ($this->reply['dataZipped']) { //数据压缩
            $this->reply['resultData'] = JCommon::jZip($this->reply['resultData']);
        }
        //获取的时候 计算执行时间
        if (!$this->reply['timeSpend']) {
            $this->reply['timeSpend'] = intval(1000 * (microtime(true) - $this->_start));
        }
        return $this->reply;
    }

    /**
     * @return Response
     */
    public function resetStart()
    {
        $this->_start = microtime(true);
        return self::$instance;
    }

    /**
     * @param int $code
     * @param string $msgContent
     * @param string $msgTitle
     * @param int $msgLevel
     * @return Response
     */
    public function setError(int $code,string $msgContent = '',string $msgTitle = '',int $msgLevel = 1)
    {
        $this->reply['resultCode'] = JCommon::toInt($code);
        $this->reply['msgContent'] = $msgContent;
        $this->reply['msgTitle'] = $msgTitle;
        $this->reply['msgLevel'] = $msgLevel;
        return self::$instance;
    }

    /**
     * @param string|int $key
     * @return mixed|null
     */
    public function getValue($key)
    {
        if (!isset($this->reply[$key])) return null;
        return $this->reply[$key];
    }

    /**
     * @param string|int $key
     * @param mixed $value
     * @return Response
     */
    public function setValue($key, $value)
    {
        $this->reply[$key] = $value;
        return self::$instance;
    }

    /**
     * @param null|int|string $key
     * @return mixed|null
     */
    public function getData($key = null)
    {
        if ($key) {
            return isset($this->reply['resultData'][$key]) ? $this->reply['resultData'][$key] : null;
        }
        return $this->reply['resultData'];
    }

    /**
     * @param mixed $value
     * @param string|int|null $key
     * @return Response
     */
    public function setData($value, $key = null)
    {
        if (!$key) {
            $this->reply['resultData'] = $value;
        } else {
            $this->reply['resultData'][$key] = $value;
        }
        return self::$instance;
    }

    /**
     * @return int
     */
    public function getMsgLevel()
    {
        return $this->reply['msgLevel'];
    }

    /**
     * @param int $msgLevel
     * @return Response
     */
    public function setMsgLevel(int $msgLevel = 1)
    {
        $this->reply['msgLevel'] = intval($msgLevel);
        return self::$instance;
    }

    /**
     * @return string
     */
    public function getMsgTitle()
    {
        return $this->reply['msgTitle'];
    }

    /**
     * @param string $msgTitle
     * @return Response
     */
    public function setMsgTitle(string $msgTitle = '')
    {
        $this->reply['msgTitle'] = $msgTitle;
        return self::$instance;
    }

    /**
     * @return string
     */
    public function getMsgContent()
    {
        return $this->reply['msgContent'];
    }

    /**
     * @param string $msgContent
     * @return Response
     */
    public function setMsgContent(string $msgContent = '')
    {
        $this->reply['msgContent'] = $msgContent;
        return self::$instance;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->reply['resultCode'];
    }

    /**
     * @param int $code
     * @return Response
     */
    public function setCode(int $code)
    {
        $this->reply['resultCode'] = JCommon::toInt($code);
        return self::$instance;
    }
}