<?php
namespace jhelper;

class JCurl
{
    public $method = 'get';//请求方法 (get | post)
    public $url;
    public $paramType = 'form';//参数提交方式 ( form | json )
    public $params = [];

    public $certFile;//证书文件
    public $certPasswd;//证书密码
    public $certType = 'PEM';//证书类型PEM

    public $caFile;//CA文件

    public $timeOut = 30;//超时时间
    public $responseCode = 0;//http状态码
    public $responseHeader = '';//http返回头
    public $responseContent = '';//应答内容
    public $errInfo = '';//错误信息

    private $diyHeaders = [];
    private $diyCookies = [];
    private $requestHeader = '';
    private $requestContent = '';

    public function __construct($url = '', $params = [], $method = 'get', $paramType = 'form')
    {
        if ($url) $this->url = $url;
        if ($params) $this->params = $params;
        if ($method) $this->method = strtolower($method);
        if ($paramType) $this->paramType = strtolower($paramType);
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    //设置请求方法post或者get
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    //设置参数格式  form 或者 json
    public function setParamType($type)
    {
        $this->paramType = $type;
        return $this;
    }

    //设置证书信息
    public function setCertInfo($certFile, $certPasswd, $certType = "PEM")
    {
        $this->certFile = $certFile;
        $this->certPasswd = $certPasswd;
        $this->certType = $certType;
        return $this;
    }

    //设置Ca
    public function setCaInfo($caFile)
    {
        $this->caFile = $caFile;
        return $this;
    }

    //设置超时时间,单位秒
    public function setTimeOut($timeOut)
    {
        $this->timeOut = $timeOut;
        return $this;
    }

    public function setUserAgent(string $value)
    {
        $this->diyHeaders['user-agent'] = $value;
        return $this;
    }

    public function setHeader(string $key, string $value)
    {
        $this->diyHeaders[$key] = $value;
        return $this;
    }

    public function setCookie(string $key, string $value)
    {
        $this->diyCookies[$key] = "$key=$value";
        return $this;
    }

    //执行http调用
    public function call()
    {
        $ch = curl_init(); //启动一个CURL会话
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeOut);// 设置curl允许执行的最长秒数
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);// 获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在

        //处理 User-Agent
        if (isset($this->diyHeaders['user-agent'])) {
            curl_setopt($ch, CURLOPT_USERAGENT, $this->diyHeaders['user-agent']);
        } else {
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 PHP cUrl');
        }

        //处理 Cookie
        if (!empty($this->diyCookies)) {
            curl_setopt($ch, CURLOPT_COOKIE, implode('; ', $this->diyCookies));
        }

        //处理 Header
        foreach ($this->diyHeaders as $key => $value) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["$key: $value"]);
        }

        $paramStr = '';
        if ($this->params) {
            if ($this->paramType == 'json') {
                $paramStr = json_encode($this->params);
            } else {
                $paramStr = http_build_query($this->params);
            }
        }

        if ($this->method == "post") {
            if ($this->paramType == 'json') {
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "Connection: keep-alive",
                    "Content-Type: application/json; charset=UTF-8", //传送的数据类型
                    "Content-Length: " . strlen($paramStr) //传送数据长度
                ]);
            }
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, $this->url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $paramStr);//要传送的所有数据
        } else {
            if ($paramStr) {
                curl_setopt($ch, CURLOPT_URL, $this->url . '?' . $paramStr);
            } else {
                curl_setopt($ch, CURLOPT_URL, $this->url);
            }
        }

        if ($this->certFile != "") { //设置证书信息
            curl_setopt($ch, CURLOPT_SSLCERT, $this->certFile);
            curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $this->certPasswd);
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, $this->certType);
        }

        if ($this->caFile != "") { //设置CA
            // 对认证证书来源的检查，0表示阻止对证书的合法性的检查。1需要设置CURLOPT_CAINFO
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_CAINFO, $this->caFile);
        } else {
            // 对认证证书来源的检查，0表示阻止对证书的合法性的检查。1需要设置CURLOPT_CAINFO
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }

        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);//开启请求头信息记录
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //以文件流形式返回
        curl_setopt($ch, CURLOPT_HEADER, 1); //返回响应头信息
        curl_setopt($ch, CURLOPT_NOBODY, 0); //不返回响应主体

        $response = curl_exec($ch);// 发送请求

        $this->requestHeader = curl_getinfo($ch, CURLINFO_HEADER_OUT);

        $this->responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($this->responseCode == 200) {
            //剥离 相应头及主体信息
            //方法一:
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $this->responseHeader = substr($response, 0, $headerSize);
            $this->responseContent = substr($response, $headerSize);

            //方法二:
            list($this->responseHeader, $this->responseContent) = explode("\r\n\r\n", $response, 2);
            curl_close($ch);
            return true;
        }

        if ($response == NULL) {
            $this->errInfo = "call http err :" . curl_errno($ch) . " - " . curl_error($ch);
            curl_close($ch);
            return false;
        } elseif ($this->responseCode != "200") {
            $this->errInfo = "call http err httpcode=" . $this->responseCode;
            curl_close($ch);
            return false;
        }
        curl_close($ch);
        return false;
    }

    public function getRequestHeader()
    {
        return $this->requestHeader;
    }

    public function getRequestContent()
    {
        $paramStr = '';
        if ($this->params) {
            if ($this->paramType == 'json') {
                $paramStr = json_encode($this->params);
            } else {
                $paramStr = http_build_query($this->params);
            }
        }
        $this->requestContent = ($paramStr ? ($this->url . '?' . $paramStr) : $this->url);
        return $this->requestContent;
    }

    public function getErrInfo()
    { //获取错误信息
        return $this->errInfo;
    }

    public function getResponseCode()
    { //获取响应状态码
        return $this->responseCode;
    }

    public function getResponseHeader()
    { //获取响应头信息
        return $this->responseHeader;
    }

    public function getResponseContent()
    { //获取响应内容
        return $this->responseContent;
    }


}