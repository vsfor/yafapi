<?php
namespace vendor\jeen\wechat;

/**
 * 微信公众号  素材管理
 * 公众号经常有需要用到一些临时性的多媒体素材的场景，例如在使用接口特别是发送消息时，对多媒体文件、多媒体消息的获取和调用等操作，是通过media_id来进行的
 * 1、对于临时素材，每个素材（media_id）会在开发者上传或粉丝发送到微信服务器3天后自动删除（所以用户发送给开发者的素材，若开发者需要，应尽快下载到本地），以节省服务器资源。
 * 2、media_id是可复用的。
 * 3、素材的格式大小等要求与公众平台官网一致。具体是，图片大小不超过2M，支持bmp/png/jpeg/jpg/gif格式，语音大小不超过2M，长度不超过60秒（公众平台官网可以在文章中插入小于30分钟的语音，但这些语音不能用于群发等场景，只能放在文章内，这方面接口暂不支持），支持mp3/wma/wav/amr格式
 * 4、需使用https调用本接口。
 * Class MpMedia
 * @package vendor\jeen\wechat
 */
class MpMedia extends Mp
{
    private static $instance;
    /**
     * @param string $appId
     * @param string $appSecret
     * @param string $token
     * @return MpMedia
     */
    public static function getInstance($appId='', $appSecret='', $token='')
    {
        $classHash = 'mp_'.md5("$appId:$appSecret:$token");
        if(empty(self::$instance[$classHash])){
            self::$instance[$classHash] = new self($appId,$appSecret,$token);
        }
        return self::$instance[$classHash];
    }

    private $apiBaseUrl = 'https://api.weixin.qq.com/cgi-bin/media/';

    /**
     * 新增临时素材   上传多媒体文件
     * @param string $type 媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）
     *上传的临时多媒体文件有格式和大小限制，如下：
     *
     * 图片（image）: 2M，支持bmp/png/jpeg/jpg/gif格式
     * 语音（voice）：2M，播放长度不超过60s，支持AMR\MP3格式
     * 视频（video）：10MB，支持MP4格式
     * 缩略图（thumb）：64KB，支持JPG格式
     * @param array|string $file form-data中媒体文件标识，有filename、filelength、content-type等信息
     * @return mixed
     * 媒体文件在后台保存时间为3天，即3天后media_id失效
     */
    public function upload($type, $file)
    {
        if(is_array($file)) {
            $fileData = '@'.realpath($file['tmp_name']);
            if(isset($file['type'])) $fileData .= ';type='.$file['type'];
            if(isset($file['name'])) $fileData .= ';filename='.$file['name'];
        } else {
            $fileData = '@'.strval($file);
        }
        $body['media'] = $fileData;

        $url = $this->makeUrl($this->apiBaseUrl, 'upload', [
            'access_token' => $this->accessToken,
            'type' => $type
        ]);
        $res = json_decode($this->httpPost($url, $body, 'form'), true);
        return $res;
    }

    /**
     * 获取临时素材   下载多媒体文件
     * 注意，视频文件不支持https下载，调用该接口需http协议
     * @param string $mediaId
     * @return mixed
     */
    public function get($mediaId)
    {
        $url = $this->makeUrl($this->apiBaseUrl, 'get', [
            'access_token' => $this->accessToken,
            'media_id' => $mediaId
        ]);
        $res = $this->httpGet($url);
        return $res;
    }


    /**
     * 上传图文消息内的图片获取URL   -- 永久型素材 ?
     * @param array|string $file  限jpg|png格式 不超过1M
     * @return mixed
     */
    public function uploadImg($file)
    {
        if(is_array($file)) {
            $fileData = '@'.realpath($file['tmp_name']);
            if(isset($file['type'])) $fileData .= ';type='.$file['type'];
            if(isset($file['name'])) $fileData .= ';filename='.$file['name'];
        } else {
            $fileData = '@'.strval($file);
        }
        $body['media'] = $fileData;

        $url = $this->makeUrl($this->apiBaseUrl, 'uploadimg', $this->getParams);
        $res = json_decode($this->httpPost($url, $body, 'form'), true);
        return $res;
    }

    /**
     * @param array $articles
     * [ 1-8 条图文
     *   [thumb_media_id:"",  图文消息缩略图的media_id，可以在基础支持-上传多媒体文件接口中获得
     *    author:"",  图文消息的作者
     *    title:"",  图文消息的标题
     *    content_source_url:"",  在图文消息页面点击“阅读原文”后的页面
     *    content:"",  图文消息页面的内容，支持HTML标签。具备微信支付权限的公众号，可以使用a标签，其他公众号不能使用
     *    digest:"",  图文消息的描述
     *    show_cover_pic:0|1  是否显示封面，1为显示，0为不显示
     *   ], ...
     * ]
     * @return mixed
     */
    public function uploadNews(array $articles)
    {
        $body = json_encode(['articles'=>$articles]);
        $url = $this->makeUrl($this->apiBaseUrl, 'uploadnews', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }



}