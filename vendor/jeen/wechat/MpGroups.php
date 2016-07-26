<?php
namespace vendor\jeen\wechat;

/**
 * 公众号 用户分组管理
 * Class MpGroups
 * 0 未分组
 * 1 黑名单
 * 2 星标组
 * 自定义分组ID  start from:  100
 * @package vendor\jeen\wechat
 */
class MpGroups extends Mp
{
    private static $instance;
    /**
     * @param string $appId
     * @param string $appSecret
     * @param string $token
     * @return MpGroups
     */
    public static function getInstance($appId='', $appSecret='', $token='')
    {
        $classHash = 'mp_'.md5("$appId:$appSecret:$token");
        if(empty(self::$instance[$classHash])){
            self::$instance[$classHash] = new self($appId,$appSecret,$token);
        }
        return self::$instance[$classHash];
    }

    private $apiBaseUrl = 'https://api.weixin.qq.com/cgi-bin/groups/';

    /**
     * 创建分组
     * @param string $groupName
     * @return mixed
     */
    public function create($groupName)
    {
        $body = json_encode(['group' => ['name' => strval($groupName)]]);
        $url = $this->makeUrl($this->apiBaseUrl, 'create', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }

    /**
     * 查询所有分组
     * @return mixed
     * [groups: [ [id,name,count] ]]
     */
    public function get()
    {
        $url = $this->makeUrl($this->apiBaseUrl, 'get', $this->getParams);
        $res = json_decode($this->httpGet($url), true);
        return $res;
    }

    /**
     * 查询用户所在分组
     * @param string $openId 通过用户的OpenID查询其所在的GroupID
     * @return mixed
     */
    public function getId($openId)
    {
        $body = json_encode(['openid'=>strval($openId)]);
        $url = $this->makeUrl($this->apiBaseUrl, 'getid', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }

    /**
     * 修改分组名
     * @param int $groupId
     * @param string $groupName
     * @return mixed
     */
    public function update($groupId, $groupName)
    {
        $body = json_encode(['group'=>['id'=>intval($groupId), 'name'=> strval($groupName)]]);
        $url = $this->makeUrl($this->apiBaseUrl, 'update', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }

    /**
     * 移动用户分组
     * @param string $openId
     * @param int $toGroupId
     * @return mixed
     */
    public function membersUpdate($openId, $toGroupId)
    {
        $body = json_encode(['openid'=>strval($openId), 'to_groupid'=>intval($toGroupId)]);
        $url = $this->makeUrl($this->apiBaseUrl, 'members/update', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }

    /**
     * 批量移动用户分组
     * @param array $openIdList
     * @param int $toGroupId
     * @return mixed
     */
    public function membersBatchUpdate(array $openIdList, $toGroupId)
    {
        $body = json_encode(['openid_list'=>$openIdList, 'to_groupid'=>intval($toGroupId)]);
        $url = $this->makeUrl($this->apiBaseUrl, 'members/batchupdate', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }

    /**
     * 删除分组
     * 删除分组后，所有该分组内的用户自动进入默认分组
     * @param int $groupId
     * @return mixed
     */
    public function delete($groupId)
    {
        $body = json_encode(['group' => ['id' => intval($groupId)]]);
        $url = $this->makeUrl($this->apiBaseUrl, 'delete', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }



}