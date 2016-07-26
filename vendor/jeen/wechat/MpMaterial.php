<?php
namespace vendor\jeen\wechat;

/**
 * 素材管理
 * Class MpMaterial
 * @package vendor\jeen\wechat
 */
class MpMaterial extends Mp
{
    private static $instance;
    /**
     * @param string $appId
     * @param string $appSecret
     * @param string $token
     * @return MpMaterial
     */
    public static function getInstance($appId='', $appSecret='', $token='')
    {
        $classHash = 'mp_'.md5("$appId:$appSecret:$token");
        if(empty(self::$instance[$classHash])){
            self::$instance[$classHash] = new self($appId,$appSecret,$token);
        }
        return self::$instance[$classHash];
    }
    
    private $apiBaseUrl = 'https://api.weixin.qq.com/cgi-bin/material/';

    /**
     * 新增永久图文素材
     * @param array $articles  1-8个
     * [
     *  [title:"",
     *   thumb_media_id:"",
     *   author:"",
     *   digest:"",
     *   show_cover_pic:"",
     *   content:"",
     *   content_source_url:""
     *  ]
     * ]
     * @return mixed
     */
    public function addNews(array $articles)
    {
        $body = json_encode(['articles' => $articles]);
        $url = $this->makeUrl($this->apiBaseUrl, 'add_news', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }

    /**
     * 新增其他类型永久素材  媒体文件
     * @param string $type
     * 媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）
     * @param array|string $file
     * @param array $extData
     * @return mixed
     */
    public function addMaterial($type, $file, $extData = [])
    {
        if(is_array($file)) {
            $fileData = '@'.realpath($file['tmp_name']);
            if(isset($file['type'])) $fileData .= ';type='.$file['type'];
            if(isset($file['name'])) $fileData .= ';filename='.$file['name'];
        } else {
            $fileData = '@'.strval($file);
        }
        $body['media'] = $fileData;
        if ($type == 'video') {
            $body['discription'] = json_encode([
                'title' => $extData['title'],//视频素材标题
                'introduction' => $extData['introduction'] //视频素材描述
            ]);
        }

        $url = $this->makeUrl($this->apiBaseUrl, 'add_material', [
            'access_token' => $this->accessToken,
            'type' => $type
        ]);
        $res = json_decode($this->httpPost($url, $body, 'form'), true);
        return $res;
    }

    /**
     * 获取永久素材
     * 图文 及 视频返回 json信息, 其他直接返回媒体文件内容 > file 保存即可
     * @param string $mediaId
     * @return mixed
     */
    public function getMaterial($mediaId)
    {
        $body = json_encode(['media_id' => strval($mediaId)]);
        $url = $this->makeUrl($this->apiBaseUrl, 'get_material', $this->getParams);
        $res = $this->httpPost($url, $body);
        return $res;
    }

    /**
     * 删除图文素材
     * @param string $mediaId
     * @return mixed
     */
    public function delMaterial($mediaId)
    {
        $body = json_encode(['media_id' => strval($mediaId)]);
        $url = $this->makeUrl($this->apiBaseUrl, 'del_material', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }

    /**
     * 修改永久图文素材
     * @param array|string $news
     * [
     *   media_id:"",
     *   index: ,要更新的文章在图文消息中的位置（多图文消息时，此字段才有意义），第一篇为0
     *   articles: [
     *     title:"",
     *     thumb_media_id:"",
     *     author:"",
     *     digest:"",
     *     show_cover_pic:
     *     content:"",
     *     content_source_url:""
     *   ]
     * ]
     * @return mixed
     */
    public function updateNews($news)
    {
        $body = is_string($news) ? $news : json_encode($news);
        $url = $this->makeUrl($this->apiBaseUrl, 'update_news', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }

    /**
     * 获取永久素材的总数
     * @return mixed
     * 图文上限5000   其他上限1000
     */
    public function getMaterialCount()
    {
        $url = $this->makeUrl($this->apiBaseUrl, 'get_materialcount', $this->getParams);
        $res = json_decode($this->httpGet($url), true);
        return $res;
    }

    /**
     * 获取素材列表
     * @param string $type 素材的类型，图片（image）、视频（video）、语音 （voice）、图文（news）
     * @param int $offset   0开始
     * @param int $count  1-20 取值
     * @return mixed
     */
    public function batchGetMaterial($type, $offset = 0, $count = 10)
    {
        $body = json_encode([
            'type' => $type,
            'offset' => $offset,
            'count' => $count
        ]);
        $url = $this->makeUrl($this->apiBaseUrl, 'update_news', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }

}