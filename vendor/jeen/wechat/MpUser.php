<?php
namespace vendor\jeen\wechat;

class MpUser extends Mp
{
    private static $instance;
    /**
     * @param string $appId
     * @param string $appSecret
     * @param string $token
     * @return MpUser
     */
    public static function getInstance($appId='', $appSecret='', $token='')
    {
        $classHash = 'mp_'.md5("$appId:$appSecret:$token");
        if(empty(self::$instance[$classHash])){
            self::$instance[$classHash] = new self($appId,$appSecret,$token); 
        }
        return self::$instance[$classHash];
    }

    private $apiBaseUrl = 'https://api.weixin.qq.com/cgi-bin/user/';

    /**
     * 设置用户备注名
     * @param string $openId
     * @param string $remark
     * @return mixed
     */
    public function infoUpdateRemark($openId, $remark)
    {
        $body = json_encode([
            'openid' => strval($openId),
            'remark' => strval($remark)
        ]);
        $url = $this->makeUrl($this->apiBaseUrl, 'info/updateremark', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }

    /**
     * 获取用户基本信息   含UnionID
     * @param string $openId 普通用户的标识，对当前公众号唯一
     * @param string $lang 返回国家地区语言版本，zh_CN 简体，zh_TW 繁体，en 英语
     * @return mixed
     */
    public function info($openId, $lang = 'zh_CN')
    {
        $url = $this->makeUrl($this->apiBaseUrl, 'info', [
            'access_token' => $this->accessToken,
            'openid' => strval($openId),
            'lang' => strval($lang)
        ]);
        $res = json_decode($this->httpGet($url), true);
        return $res;
    }

    /**
     * 批量获取用户基本信息    含UnionID
     * @param array $openIdList  openID 数组
     * @param string $lang  国家地区语言版本，zh_CN 简体，zh_TW 繁体，en 英语，默认为zh-CN
     * @param array $userList  用户列表(高优先级)
     * [
     *  [openid:"", lang:""]
     * ]
     * @return mixed
     */
    public function infoBatchGet(array $openIdList, $lang = 'zh_CN', $userList = [])
    {
        if (!$userList) {
            $userList = [];
            foreach ($openIdList as $openId) {
                $userList[] = [
                    'openid' => strval($openId),
                    'lang' => strval($lang)
                ];
            }
        }
        $body = json_encode(['user_list' => $userList]);
        $url = $this->makeUrl($this->apiBaseUrl, 'info/batchget', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }

    /**
     * 获取用户列表 一次拉取调用最多拉取10000个关注者的OpenID
     * @param string $nextOpenId
     * @return mixed
     */
    public function get($nextOpenId='')
    {
        $url = $this->makeUrl($this->apiBaseUrl, 'get', [
            'access_token' => $this->accessToken,
            'next_openid' => strval($nextOpenId)
        ]);
        $res = json_decode($this->httpGet($url), true);
        return $res;
    }



}