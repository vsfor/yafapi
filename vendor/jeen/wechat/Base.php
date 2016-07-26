<?php
namespace vendor\jeen\wechat;

use core\JObject;

class Base extends JObject
{
/**
 *
 * 如果是企业号用以下URL获取access_token
 * $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
 *
 * 如果是公众号用以下URL获取access_token
 * $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
 */

    /**
     * xml 字符串转换为 对象或数组
     * @param string $xmlString
     * @param bool $returnObj
     * @return object|array
     */
    public function xmlDecode($xmlString, $returnObj = false)
    {
        /** @var $xmlString \SimpleXMLElement */
        if (is_string($xmlString)) {
            libxml_disable_entity_loader(true);
            $xmlObj = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);
        } else {
            $xmlObj = $xmlString;
        }
        var_dump($xmlObj->children());
        foreach ($xmlObj as $key => $value) {
            if (strlen($key) > 4 && substr($key, 0, 2) == '__' && substr($key, strlen($key) - 2, 2) == '__') {
                $newKey = substr($key, 2, strlen($key) - 4);
                $xmlObj->$newKey = $this->xmlDecode($value, $returnObj);
            } else {
                $xmlObj->$key = $this->xmlDecode($value, $returnObj);;
            }
        }
        if ($returnObj) {
            return $xmlObj;
        } else {
            return (array)($xmlObj);
        }
    }

    /**
     * 数组转换为xml 字符串
     * @param array|string $data
     * @param string $root
     * @param bool $CDATA
     * @param bool $head
     * @param int $level
     * @return string
     */
    public function xmlEncode($data, $root = '', $CDATA = true, $head = false, $level = 0)
    {
        $prefix = str_repeat("    ", $level);
        $tmp = '';
        if (strlen($root) == 0) {
            $tmp .= $head ? "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<xml>" : "<xml>";
        } else {
            if (is_numeric(substr($root,0,1))) {
                $root = "__{$root}__";
            }
            $tmp .= "$prefix<$root>";
        }
        if (!is_array($data)) {
            if ($CDATA) $data = "<![CDATA[$data]]>";
            $tmp .= $data;
        } else {
            $tmp .= "\n";
            foreach ($data as $key => $value) {
                $tmp .= $this->xmlEncode($value, $key, $CDATA, false, $level+1)."\n";
            }
            $tmp .= $prefix;
        }
        $tmp .= (strlen($root) != 0 ? "</$root>" : "</xml>\n");
        return $tmp;
    }
    
    

    protected function makeUrl($baseUrl, $uri='', $getParams = [])
    {
        if ($getParams) {
            return $baseUrl . $uri . '?' . http_build_query($getParams);
        } else {
            return $baseUrl . $uri;
        }
    }

    protected function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 300);
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }

    protected function httpPost($url, $body, $type='json')
    {
        if (!is_string($body)) {
            if ($type == 'json') {
                $body = json_encode($body);
            } else {
                $body = http_build_query($body);
            }
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 300);
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        if ($type == 'json') {
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                "Connection: keep-alive",
                "Content-Type: application/json; charset=UTF-8", //传送的数据类型
                "Content-Length: ".strlen($body) //传送数据长度
            ]);
        } else {
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                "Connection: keep-alive"
            ]);
        }
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);//要传送的所有数据

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }

    protected function getTokenKeyInfo($key)
    {
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $fileName = $key . '.php';
        return json_decode($this->get_php_file($fileName));
    }
    
    protected function setTokenKeyInfo($key, $value)
    {
        $fileName = $key . '.php';
        if (!is_string($value)) {
            $value = json_encode($value);
        }
        return $this->set_php_file($fileName, $value);
    }

    protected function get_php_file($filename) {
        $filename = dirname(__FILE__).'/'.$filename;
        if (!file_exists($filename)) {
            @touch($filename); @chmod($filename,0777);
        }
        return trim(substr(file_get_contents($filename), 15));
    }

    protected function set_php_file($filename, $content) {
        $filename = dirname(__FILE__).'/'.$filename;
        $fp = fopen($filename, "w");
        fwrite($fp, "<?php exit();?>" . $content);
        fclose($fp);
        return true;
    }

}