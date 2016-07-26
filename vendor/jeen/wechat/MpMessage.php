<?php
namespace vendor\jeen\wechat;

/**
 * 消息管理
 * Class MpMessage
 * @package vendor\jeen\wechat
 */
class MpMessage extends Mp
{
    private static $instance;
    /**
     * @param string $appId
     * @param string $appSecret
     * @param string $token
     * @return MpMessage
     */
    public static function getInstance($appId='', $appSecret='', $token='')
    {
        $classHash = 'mp_'.md5("$appId:$appSecret:$token");
        if(empty(self::$instance[$classHash])){
            self::$instance[$classHash] = new self($appId,$appSecret,$token);
        }
        return self::$instance[$classHash];
    }

    private $apiBaseUrl = 'https://api.weixin.qq.com/cgi-bin/message/';


    /**
     * 客服接口 发消息
     * @param array|string $message  ['touser','msgtype',...]
     * touser  OPENID
     * msgtype  text|image|voice|video|music|news|mpnews|wxcard
     * 文本 text [content:""]
     * 图片 image [media_id:""]
     * 语音 voice [media_id:""]
     * 视频 video [media_id:"",thumb_media_id:"",title:"",description:""]
     * 音乐 music [title:"",description:"",musicurl:"",hqmusicurl:"",thumb_media_id:""]
     * 图文(外链) news  [articles: [[title:"",description:"",url:"",picurl:""],[...]...]]
     * 图文(消息页) mpnews [media_id:""]
     * 卡券 wxcard [card_id:"", card_ext:"{code:"",openid:"",timestamp:"",signature:""}"]
     *
     * 指定客服发送
     * $message [..., 'customservice':['kf_account':""]]
     *
     * 图片链接支持 jpg,png  大图 640*320  小图 80*80
     * @return mixed
     */
    public function customSend($message)
    {
        $url = $this->makeUrl($this->apiBaseUrl, 'custom/send', $this->getParams);
        $body = is_string($message) ? $message : json_encode($message);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }


    /**
     * 根据 分组 进行群发
     * @param array|string $message
     * [
     * filter: [ ---- 分组群发
     *     is_to_all: 用于设定是否向全部用户发送，值为true或false，选择true该消息群发给所有用户，选择false可根据group_id发送给指定群组的用户
     *     group_id: 群发到的分组的group_id，参加用户管理中用户分组接口，若is_to_all值为true，可不填写group_id
     *   ]
     *
     * msgtype: "" 群发的消息类型，图文消息为mpnews，文本消息为text，语音为voice，音乐为music，图片为image，视频为video，卡券为wxcard
     *
     * mpnews: [media_id:""]
     * text: [content:""]
     * voice: [media_id:""]
     * music:
     * image: [media_id:""]
     * mpvideo: [media_id:""] 注意: 此处的media_id 为使用media/uploadvideo提交[media_id,title,description]返回[type:"video",media_id:"此处的media_id",created_at:...]中的media_id
     * wxcard: [card_id:""]
     *
     * ]
     * @return mixed
     */
    public function massSendAll($message)
    {
        $body = is_string($message) ? $message : json_encode($message);
        $url = $this->makeUrl($this->apiBaseUrl, 'mass/sendall', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }


    /**
     * 根据 OpenID列表  进行群发
     * @param array|string $message
     * [
     * touser: ["OpenId1","OpenId2"] ---OpenID 列表群发
     *
     * msgtype: "" 群发的消息类型，图文消息为mpnews，文本消息为text，语音为voice，音乐为music，图片为image，视频为video，卡券为wxcard
     *
     * mpnews: [media_id:""]
     * text: [content:""]
     * voice: [media_id:""]
     * music:
     * image: [media_id:""]
     * mpvideo: [media_id:"",title:"",description:""] 注意: 此处的media_id 为使用media/uploadvideo提交[media_id,title,description]返回[type:"video",media_id:"此处的media_id",created_at:...]中的media_id
     * wxcard: [card_id:""]
     *
     * ]
     * @return mixed
     */
    public function massSend($message)
    {
        $body = is_string($message) ? $message : json_encode($message);
        $url = $this->makeUrl($this->apiBaseUrl, 'mass/send', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }

    /**
     * 删除群发    群发只有在刚发出的半小时内可以删除，发出半小时之后将无法被删除
     * @param int $msgId
     * @return mixed
     */
    public function massDelete($msgId)
    {
        $body = json_encode(['msg_id'=>intval($msgId)]);
        $url = $this->makeUrl($this->apiBaseUrl, 'mass/delete', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }

    /**
     * 群发消息预览接口  每日调用次数有限制（100次）
     * @param array|string $message
     * [
     * touser: "OpenId1" ---OpenID
     * towxname: "示例的微信号"  优先级高于 touser   2选一 即可
     *
     * msgtype: "" 群发的消息类型，图文消息为mpnews，文本消息为text，语音为voice，音乐为music，图片为image，视频为video，卡券为wxcard
     *
     * mpnews: [media_id:""]
     * text: [content:""]
     * voice: [media_id:""]
     * music:
     * image: [media_id:""]
     * mpvideo: [media_id:""] 注意: 此处的media_id 为使用media/uploadvideo提交[media_id,title,description]返回[type:"video",media_id:"此处的media_id",created_at:...]中的media_id
     * wxcard: [card_id:"",card_ext:"{code:"",openid:"",timestamp:"",signature:""}"]
     *
     * ]
     * @return mixed
     */
    public function massPreview($message)
    {
        $body = is_string($message) ? $message : json_encode($message);
        $url = $this->makeUrl($this->apiBaseUrl, 'mass/preview', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }


    /**
     * 查询群发消息 发送状态
     * @param int $msgId
     * @return mixed
     * 若群发任务提交成功，则在群发任务结束时，
     * 会向开发者在公众平台填写的开发者URL推送事件
     *
     * 需要注意，由于群发任务彻底完成需要较长时间，
     * 将会在群发任务即将完成的时候，就推送群发结果，
     * 此时的推送人数数据将会与实际情形存在一定误差
     */
    public function massGet($msgId)
    {
        $body = json_encode(['msg_id'=>$msgId]);
        $url = $this->makeUrl($this->apiBaseUrl, 'mass/get', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }

    

    /**
     * 发送模板消息    发送后 有事件推送
     * @param array|string $message
     * [
     *   touser: "OPENID"
     *   template_id: ""
     *   url: ""
     *   data: [
     *     keyName: [keyValue:"", color:"#666777"]
     *     ...
     *   ]
     * ]
     * @return mixed
     */
    public function templateSend($message)
    {
        $body = is_string($message) ? $message : json_encode($message);
        $url = $this->makeUrl($this->apiBaseUrl, 'template/send', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }


}