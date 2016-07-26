<?php
namespace vendor\jeen\wechat;

class Mp extends Base
{
    protected $appId='wx3d28681102af2953';
    protected $appSecret='01490b65562dd5a119074630d97702fa';
    protected $accessToken;
    protected $token = 'jeen'; 
    protected $getParams = [];
    protected $postParams = [];
    private $apiBaseUrl = 'https://api.weixin.qq.com/cgi-bin/';
    private static $instance;

    /**
     * @param string $appId
     * @param string $appSecret
     * @param string $token
     * @return Mp
     */
    public static function getInstance($appId='', $appSecret='', $token='')
    {
        $classHash = 'mp_'.md5("$appId:$appSecret:$token");
        if(empty(self::$instance[$classHash])){
            self::$instance[$classHash] = new self($appId,$appSecret,$token);
        }
        return self::$instance[$classHash];
    }
    protected function __construct($appId='', $appSecret='', $token='')
    {
        if ($appId) $this->appId = $appId;
        if ($appSecret) $this->appSecret = $appSecret;
        if ($token) $this->token = $token;
        $this->accessToken = $this->getAccessToken();
        $this->getParams['access_token'] = $this->accessToken;
    }
    
    public function checkSignature()
    {
        if (!isset($_GET["signature"], $_GET["timestamp"], $_GET["nonce"])) {
            return false;
        }
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = $this->token;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        }
        return false;
    }
    
    public function responseMsg($postStr)
    { 
        //extract post data
        if (!empty($postStr)) {
            /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
               the best way is to check the validity of xml by yourself */
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $time = time();
            $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
            if (!empty($keyword)) {
                $msgType = "text";
                $contentStr = "$keyword";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
            } else {
                $msgType = "text";
                $contentStr = json_encode($postObj);
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
            }
        } else {
            echo "";
            exit;
        }
    }

    /**
     * 获取 接口调用凭据
     * @return mixed
     * @throws \Exception
     */
    protected function getAccessToken()
    {
        $tokenKey = 'WXTK_' . md5($this->appId.':'.$this->appSecret);
        $data = $this->getTokenKeyInfo($tokenKey);
        if (!$data || $data->expire_time < time()) {
            $url = $this->makeUrl($this->apiBaseUrl, 'token', [
                'grant_type' => 'client_credential',
                'appid' => $this->appId,
                'secret' => $this->appSecret
            ]);
            $res = json_decode($this->httpGet($url));
            if (!isset($res->access_token)) {
                throw new \Exception("WeiXin Mp Access Token Get Failed");
            }
            $this->accessToken = $res->access_token;
            if ($this->accessToken) {
                $data = new \stdClass();
                $data->expire_time = time() + 3666;
                $data->fresh_date = date("Y-m-d H:i:s");
                $data->access_token = $this->accessToken;
                $this->setTokenKeyInfo($tokenKey, $data);
            }
        } else {
            $this->accessToken = $data->access_token;
        } 
        return $this->accessToken;
    }

    /**
     * 获取 微信服务器IP地址列表
     * @return mixed
     */
    public function getCallbackIP()
    {
        $url = $this->makeUrl($this->apiBaseUrl, "getcallbackip", $this->getParams);
        $res = json_decode($this->httpGet($url), true);
        return $res;
    }

    /**
     * 获取自定义菜单配置接口  通过网站功能或API发布菜单 均可获取
     * @return mixed
     *
     * 参数	说明
     * is_menu_open	菜单是否开启，0代表未开启，1代表开启
     * selfmenu_info	菜单信息
     * button	菜单按钮
     * type	菜单的类型，公众平台官网上能够设置的菜单类型有view（跳转网页）、text（返回文本，下同）、img、photo、video、voice。使用API设置的则有8种，详见《自定义菜单创建接口》
     * name	菜单名称
     * value、url、key等字段	对于不同的菜单类型，value的值意义不同。官网上设置的自定义菜单：Text:保存文字到value； Img、voice：保存mediaID到value； Video：保存视频下载链接到value； News：保存图文消息到news_info，同时保存mediaID到value； View：保存链接到url。使用API设置的自定义菜单： click、scancode_push、scancode_waitmsg、pic_sysphoto、pic_photo_or_album、	pic_weixin、location_select：保存值到key；view：保存链接到url
     * news_info	图文消息的信息
     * title	图文消息的标题
     * digest	摘要
     * author	作者
     * show_cover	是否显示封面，0为不显示，1为显示
     * cover_url	封面图片的URL
     * content_url	正文的URL
     * source_url	原文的URL，若置空则无查看原文入口
     */
    public function getCurrentSelfmenuInfo()
    {
        $url = $this->makeUrl($this->apiBaseUrl, 'get_current_selfmenu_info', $this->getParams);
        $res = json_decode($this->httpGet($url), true);
        return $res;
    }
    
    /**
     * 获取自动回复规则
     * 开发者可以通过该接口，获取公众号当前使用的自动回复规则，包括关注后自动回复、消息自动回复（60分钟内触发一次）、关键词自动回复
     * @return mixed
     */
    public function getCurrentAutoReplyInfo()
    {
        $url = $this->makeUrl($this->apiBaseUrl, 'get_current_auto_reply_info', $this->getParams);
        $res = json_decode($this->httpGet($url), true);
        return $res;
    }
    
    /**
     * 创建二维码ticket   临时
     * @param int $sceneId
     * @param int $timeOut
     * @return mixed
     */
    public function qrCodeCreate($sceneId, $timeOut = 2590000)
    {
        $body = json_encode([
            'expire_seconds' => intval($timeOut),
            'action_name'=>'QR_SCENE',
            'action_info'=>[
                'scene' => [
                    'scene_id'=>intval($sceneId)
                ]
            ]
        ]);
        $url = $this->makeUrl($this->apiBaseUrl, 'qrcode/create', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }

    /**
     * 创建二维码ticket   永久
     * @param int $sceneId  优先级较高
     * @param string $sceneStr
     * @return mixed
     */
    public function qrCodeCreateLimit($sceneId = 0, $sceneStr = '')
    {
        if ($sceneId) {
            $body = json_encode([
                'action_name'=>'QR_LIMIT_SCENE',
                'action_info'=>[
                    'scene' => [
                        'scene_id'=>intval($sceneId)
                    ]
                ]
            ]);
        } elseif ($sceneStr) {
            $body = json_encode([
                'action_name'=>'QR_LIMIT_STR_SCENE',
                'action_info'=>[
                    'scene' => [
                        'scene_str'=>strval($sceneId)
                    ]
                ]
            ]);
        } else {
            return ['errcode'=>9001001,'errmsg'=>'diy post params error'];
        }

        $url = $this->makeUrl($this->apiBaseUrl, 'qrcode/create', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }

    /**
     * 通过 ticket 获取二维码
     * @param string $ticket
     * @return mixed
     */
    public function showQrCode($ticket)
    {
        $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($ticket);
        $res = $this->httpGet($url);
        return $res;
    }

    /**
     * 长链接转短链接接口
     * 主要使用场景： 开发者用于生成二维码的原链接（商品、支付二维码等）太长导致扫码速度和成功率下降，将原长链接通过此接口转成短链接再生成二维码将大大提升扫码速度和成功率
     * @param string $longUrl
     * @return mixed
     */
    public function shortUrl($longUrl)
    {
        $body = json_encode([
            'action' => 'long2short',
            'long_url' => strval($longUrl)
        ]);

        $url = $this->makeUrl($this->apiBaseUrl, 'shorturl', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }

}