<?php
namespace vendor\jeen\wechat;

/**
 * 自定义菜单
 * Class MpMenu
 * @package vendor\jeen\wechat
 */
class MpMenu extends Mp
{
    private static $instance;
    /**
     * @param string $appId
     * @param string $appSecret
     * @param string $token
     * @return MpMenu
     */
    public static function getInstance($appId='', $appSecret='', $token='')
    {
        $classHash = 'mp_'.md5("$appId:$appSecret:$token");
        if(empty(self::$instance[$classHash])){
            self::$instance[$classHash] = new self($appId,$appSecret,$token);
        }
        return self::$instance[$classHash];
    }

    private $apiBaseUrl = 'https://api.weixin.qq.com/cgi-bin/menu/';

    /**
     * 自定义菜单 创建接口
     * @param array|string $menu
     * @param bool $handle
     *
     * 参数	是否必须	说明
     * button	是	一级菜单数组，个数应为1~3个
     * sub_button	否	二级菜单数组，个数应为1~5个
     * type	是	菜单的响应动作类型
     * name	是	菜单标题，不超过16个字节，子菜单不超过40个字节
     * key	click等点击类型必须	菜单KEY值，用于消息接口推送，不超过128字节
     * url	view类型必须	网页链接，用户点击菜单可打开链接，不超过1024字节
     * media_id	media_id类型和view_limited类型必须	调用新增永久素材接口返回的合法media_id
     *
     * @return mixed
     */
    public function create($menu, $handle = true)
    {
        $url = $this->makeUrl($this->apiBaseUrl, 'create', $this->getParams);
        $body = is_string($menu) ? $menu : json_encode($menu);
        if ($handle) {
            $res = json_decode($this->httpPost($url, $body), true);
        } else {
            $res = $this->httpPost($url, $body);
        }
        return $res;
    }

    /**
     * 自定义菜单 查询接口
     * @param bool $handle
     * @return mixed
     * 注：menu为默认菜单，conditionalmenu为个性化菜单列表
     */
    public function get($handle = true)
    {
        $url = $this->makeUrl($this->apiBaseUrl, 'get', $this->getParams);
        if ($handle) {
            $res = json_decode($this->httpGet($url), true);
        } else {
            $res = $this->httpGet($url);
        }
        return $res;
    }

    /**
     * 自定义菜单 删除接口
     * @return mixed
     */
    public function delete()
    {
        $url = $this->makeUrl($this->apiBaseUrl, 'delete', $this->getParams);
        $res = json_decode($this->httpGet($url), true);
        return $res;
    }

    /**
     * 个性化菜单 创建接口
     * @param array|string $menu
     * @param bool $handle
     * @return mixed
     */
    public function addConditional($menu, $handle = true)
    {
        $url = $this->makeUrl($this->apiBaseUrl, 'addconditional', $this->getParams);
        $body = is_string($menu) ? $menu : json_encode($menu);
        if ($handle) {
            $res = json_decode($this->httpPost($url, $body), true);
        } else {
            $res = $this->httpPost($url, $body);
        }
        return $res;
    }

    /**
     * 个性化菜单 删除接口
     * @param string|int $menuid
     * @return mixed
     */
    public function delConditional($menuid)
    {
        $url = $this->makeUrl($this->apiBaseUrl, 'delconditional', $this->getParams);
        $body = json_encode(['menuid'=>strval($menuid)]);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }

    /**
     * 测试个性化菜单匹配结果
     * @param string $userId OpenId 或 微信号
     * @param bool $handle
     * @return mixed
     */
    public function tryMatch($userId, $handle = true)
    {
        $url = $this->makeUrl($this->apiBaseUrl, 'trymatch', $this->getParams);
        $body = json_encode(['user_id'=>strval($userId)]);
        if ($handle) {
            $res = json_decode($this->httpPost($url, $body), true);
        } else {
            $res = $this->httpPost($url, $body);
        }
        return $res;
    }


}