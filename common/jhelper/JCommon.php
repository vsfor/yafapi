<?php
namespace jhelper;
 
class JCommon
{
    /**
     * 获取 Url 相对路径
     * @param $uri
     * @param array $params
     * @return string
     */
    public static function getUrl($uri, $params = [])
    {
        $baseUri = self::getBaseUrl();
        $params = empty($params) ?  '' : '?'.http_build_query($params);
        return $baseUri . $uri . $params;
    }

    /**
     * 获取 yaf 标准的 url   如  /module/controller/action/key/value
     *  $params eg: array('_m'=>module,'_c'=>controller,'_a'=>action,..)
     * @param array $params
     * @return string
     */
    public static function getLink($params = array())
    {
        $request = \Yaf\Dispatcher::getInstance()->getRequest();
        $uri = array();
        $uri['m'] = isset($params['_m']) ? $params['_m'] : strtolower($request->getModuleName());
        $uri['c'] = isset($params['_c']) ? $params['_c'] : strtolower($request->getControllerName());
        $uri['a'] = isset($params['_a']) ? $params['_a'] : strtolower($request->getActionName());
        $baseUri = self::getBaseUrl();
        $res = $baseUri . implode('/',$uri);
        if(isset($params['_m'])) unset($params['_m']);
        if(isset($params['_c'])) unset($params['_c']);
        if(isset($params['_a'])) unset($params['_a']);
        if(!empty($params)) {
            $paramstr = http_build_query($params);
            $paramstr = str_replace('=','/',$paramstr);
            $paramstr = str_replace('&','/',$paramstr);
            $res .= '/' . $paramstr;
        }
        return $res;
    }
  
    public static function getBaseUrl()
    {
        $baseUri = \Yaf\Dispatcher::getInstance()->getRequest()->getBaseUri();
        return $baseUri . '/';
    }
 
    public static function getUserIp()
    {
        if (getenv("HTTP_X_FORWARDED_FOR")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("HTTP_CLIENT_IP")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } else if (getenv("REMOTE_ADDR")) {
            $ip = getenv("REMOTE_ADDR");
        } else {
            $ip = null;
        }
        return $ip;
    }
    /**
     * 检测IP 地址是否为合法字符串
     * @param $ip
     * @return bool
     */
    public static function CheckIp($ip)
    {
        $ipArr = explode('.', $ip);
        if (count($ipArr) != 4) {
            return false;
        }
        if (is_numeric(str_replace('.', '', $ip)) === false) {
            return false;
        }
        return true;
    }
 
    /**
     * 判断指定IP 是否在一个IP段里
     * @param string $ip 指定IP
     * @param string $network IP段  例：192.168.1.100/24
     * @return bool
     */
    public static function ipInNetwork($ip, $network)
    {
        $s = explode('/', $network);
        if (!isset($s[1])) {
            return $ip == $network;
        }
        $mask = str_pad(str_repeat('1',$s[1]), 32, '0', STR_PAD_RIGHT);

        $sIp = decbin(ip2long($s[0]));
        $sNet = ($sIp & $mask);

        $cIp = decbin(ip2long($ip));
        $cNet = ($cIp & $mask);

        return $cNet == $sNet;
    }

    /**
     * 整型转换   排除直接 intval('23a3d') == 23 的情况
     * @param $str
     * @return int
     */
    public static function toInt($str)
    {
        return is_numeric($str) ? intval($str) : 0;
    }

    /**
     * xml 字符串转换为数组
     * @param $xmlStr
     * @return mixed
     */
    public static function xmlToArr($xmlStr)
    {
        $xmlStr = trim(strval($xmlStr));
        $ret = json_decode(json_encode((array) simplexml_load_string($xmlStr)), true);
        return $ret;
    }

    /**
     * 数组转换为xml 字符串
     * @param $arr
     * @param int $dom
     * @param int $item
     * @return string
     */
    public static function arrToXml($arr, $root = 'response', $dom=0, $item=0)
    {
        if (!$dom) {
            $dom = new \DOMDocument("1.0","UTF-8");
        }
        if (!$item) {
            $item = $dom->createElement($root);
            $dom->appendChild($item);
        }
        foreach ($arr as $key=>$val) {
            $itemx = $dom->createElement(is_string($key)?$key:"item");
            $item->appendChild($itemx);
            if (!is_array($val)) {
                $text = $dom->createTextNode($val);
                $itemx->appendChild($text);
            } else {
                self::arrToXml($val,$dom,$itemx);
            }
        }
        return $dom->saveXML();
    }

    /**
     * HTML 输入字符串  过滤及转换
     * @param $str
     * @return string
     */
    public static function htmlIn($str)
    {
        return htmlentities(strip_tags($str), ENT_QUOTES);
    }

    /**
     * HTML 输出字符串 反转一些特殊字符
     * @param $str
     * @return string
     */
    public static function htmlOut($str)
    {
        return html_entity_decode($str, ENT_QUOTES);
    }


    //递归解析Json字符串或数组Json键值
    public static function handleJsonParams($t)
    {
        if(is_numeric($t) || is_bool($t)) return $t;
        if(is_string($t)) {
            $temp = json_decode($t,true,512,JSON_BIGINT_AS_STRING);
            if($temp === null || $temp === false) return $t;
            if(is_int($temp)) {
                $tArr = str_split($t);
                return $tArr[0] ? $temp : $t;
            } else if(!is_array($temp)) {
                return $temp;
            }
            $t = json_decode($t,true,512,JSON_BIGINT_AS_STRING);
            foreach($t as $k=>$v) {
                $t[$k] = self::handleJsonParams($v);
            }
        } else if(is_array($t)) {
            foreach($t as $k=>$v) {
                $t[$k] = self::handleJsonParams($v);
            }
        } else {
            return [];
        }
        return $t;
    }

    /**
     * 将类似经过 http_build_query 封装的字符串  解析为数组
     * 实现类似http_parse_params的功能  最多解析3层数组
     * @param string $str
     * @return array
     */
    public function handleUrlParams($str)
    {
        $paramL = explode('&', $str);
        $final = [];
        foreach ($paramL as $paramKV) {
            list($key, $val) = explode('=', $paramKV);
            $tk = urldecode($key);
            $tv = urldecode($val);
            $tk = str_replace(']', '', $tk);
            $pka = explode('[', $tk);
            $v = count($pka);
            if ($v == 1) {
                $final[$tk] = $tv;
            } else if ($v == 2) {
                $final[$pka[0]][$pka[1]] = $tv;
            } else if ($v == 3) {
                $final[$pka[0]][$pka[1]][$pka[2]] = $tv;
            } else {
                $final[$tk] = $tv;
            }
        }
        return $final;
    }

    /**
     * 加密函数  可用jDecode()函数解密
     * @param mixed $data  待加密的字符串或数组
     * @param string $key   密钥
     * @param int $expire 过期时间
     * @return string
     */
    public static function jEncode($data,$key='',$expire = 0)
    {
        $string=serialize($data);
        $ckey_length = 4;
        $key = md5($key);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = substr(md5(microtime()), -$ckey_length);
        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);
        $string = sprintf('%010d', $expire ? $expire + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = array();
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        return $keyc.str_replace('=', '', base64_encode($result));
    }

    /**
     * 解密函数  解析使用jEncode加密得到的字符串
     * @param $string
     * @param string $key 密钥
     * @return mixed|string
     */
    public function jDecode($string,$key='')
    {
        $ckey_length = 4;
        $key = md5($key);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = substr($string, 0, $ckey_length);
        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);
        $string = base64_decode(substr($string, $ckey_length));
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = [];
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for($a = $j = $i = 0; $i < $string_length; $i++){
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if((substr($result,0,10)==0 || substr($result,0,10)-time()>0) && substr($result,10,16) == substr(md5(substr($result,26).$keyb),0,16)) {
            return unserialize(substr($result, 26));
        } else {
            return '';
        }
    }

    /**
     * 压缩 -- 加入了 urlencode , 用于解决 js 解压缩时的中文编码问题
     * @param array|string|int $obj
     * @return string
     */
    public static function jZip($obj)
    {
        //放到js中 解压  空格 变 + 号
//        return base64_encode(gzcompress(urlencode(json_encode($obj))));

        return base64_encode(gzcompress(rawurlencode(json_encode($obj))));
    }

    /**
     * 解压缩 -- 加入了 urldecode , 用于解决 js 压缩时的中文编码问题
     * @param string $str
     * @return mixed
     */
    public static function jUnzip($str)
    {
        return json_decode(rawurldecode(gzuncompress(base64_decode($str))));
    }

}