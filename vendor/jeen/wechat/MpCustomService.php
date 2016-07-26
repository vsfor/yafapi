<?php
namespace vendor\jeen\wechat;

/**
 * 多客服功能   10
 * Class MpCustomService
 * @package vendor\jeen\wechat
 *
 * 返回码	说明
 * 0	成功(no error)
 * 61451	参数错误(invalid parameter)
 * 61452	无效客服账号(invalid kf_account)
 * 61453	账号已存在(kf_account exsited)
 * 61454	账号名长度超过限制(前缀10个英文字符)(invalid kf_acount length)
 * 61455	账号名包含非法字符(英文+数字)(illegal character in kf_account)
 * 61456	账号个数超过限制(100个客服账号)(kf_account count exceeded)
 * 61457	无效头像文件类型(invalid file type)
 */
class MpCustomService extends Mp
{
    private static $instance;
    /**
     * @param string $appId
     * @param string $appSecret
     * @param string $token
     * @return MpCustomService
     */
    public static function getInstance($appId='', $appSecret='', $token='')
    {
        $classHash = 'mp_'.md5("$appId:$appSecret:$token");
        if(empty(self::$instance[$classHash])){
            self::$instance[$classHash] = new self($appId,$appSecret,$token);
        }
        return self::$instance[$classHash];
    }

    private $apiBaseUrl = 'https://api.weixin.qq.com/customservice/';

    /**
     * 获取所有客服账号
     * @return mixed
     *
     * kf_account	完整客服账号，格式为：账号前缀@公众号微信号
     * kf_headimgurl  客服头像
     * kf_nick	客服昵称
     * kf_id	客服工号
     */
    public function getKfList()
    {
        $url = $this->makeUrl($this->apiBaseUrl, 'getkflist', $this->getParams);
        $res = json_decode($this->httpGet($url), true);
        return $res;
    }

    /**
     * 获取在线客服接待信息
     * @return mixed
     *
     * kf_account	完整客服账号，格式为：账号前缀@公众号微信号
     * status	客服在线状态 1：pc在线，2：手机在线。若pc和手机同时在线则为 1+2=3
     * kf_id	客服工号
     * auto_accept	客服设置的最大自动接入数
     * accepted_case	客服当前正在接待的会话数
     */
    public function getOnlineKfList()
    {
        $url = $this->makeUrl($this->apiBaseUrl, 'getonlinekflist', $this->getParams);
        $res = json_decode($this->httpGet($url), true);
        return $res;
    }

    /**
     * 添加客服账号
     * @param array|string $kfAccount
     * [
     *   kf_account: "", 完整客服账号，格式为：账号前缀@公众号微信号，账号前缀最多10个字符，必须是英文或者数字字符。如果没有公众号微信号，请前往微信公众平台设置。
     *   nickname: "", 客服昵称，最长6个汉字或12个英文字符
     *   password: ""  客服账号登录密码，格式为密码明文的32位加密MD5值
     * ]
     * @return mixed
     */
    public function kfAccountAdd($kfAccount)
    {
        $url = $this->makeUrl($this->apiBaseUrl, 'kfaccount/add', $this->getParams);
        $body = is_string($kfAccount) ? $kfAccount : json_encode($kfAccount);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }

    /**
     * 修改客服账号
     * @param array|string $kfAccount
     * [
     *   kf_account: "", 完整客服账号，格式为：账号前缀@公众号微信号，账号前缀最多10个字符，必须是英文或者数字字符。如果没有公众号微信号，请前往微信公众平台设置。
     *   nickname: "", 客服昵称，最长6个汉字或12个英文字符
     *   password: ""  客服账号登录密码，格式为密码明文的32位加密MD5值
     * ]
     * @return mixed
     */
    public function kfAccountUpdate($kfAccount)
    {
        $url = $this->makeUrl($this->apiBaseUrl, 'kfaccount/update', $this->getParams);
        $body = is_string($kfAccount) ? $kfAccount : json_encode($kfAccount);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }


    /**
     * 设置客服账号的头像
     * @param string $kfAccount 完整客服账号，格式为：账号前缀@公众号微信号
     * @param array|string $file 图片类型只允许 jpg  推荐640*640
     * @return mixed
     */
    public function kfAccountUploadHeadimg($kfAccount, $file)
    {
        if(is_array($file)) {
            $fileData = '@'.realpath($file['tmp_name']);
            if(isset($file['type'])) $fileData .= ';type='.$file['type'];
            if(isset($file['name'])) $fileData .= ';filename='.$file['name'];
        } else {
            $fileData = '@'.strval($file);
        }
        $body = [];
        $body['media'] = $fileData;

        $url = $this->makeUrl($this->apiBaseUrl, 'kfaccount/updateheadimg',[
            'access_token' => $this->accessToken,
            'kf_account' => $kfAccount
        ]);
        $res = json_decode($this->httpPost($url, $body, 'form'), true);
        return $res;
    }

    /**
     * 删除客服账号
     * @param string $kfAccount 完整客服账号，格式为：账号前缀@公众号微信号
     * @return mixed
     */
    public function kfAccountDel($kfAccount)
    {
        $url = $this->makeUrl($this->apiBaseUrl, 'kfaccount/del', [
            'access_token' => $this->accessToken,
            'kf_account' => $kfAccount
        ]);
        $res = json_decode($this->httpGet($url), true);
        return $res;
    }

    /**
     * 为多客服的客服工号创建会话，将某个客户直接指定给客服工号接待
     * 注意此接口不会受客服自动接入数以及自动接入开关限制。只能为在线的客服（PC客户端在线，或者已绑定多客服助手）创建会话
     * @param string $kfAccount 完整客服账号，格式为：账号前缀@公众号微信号
     * @param string $openId 客户openid
     * @param string $text 附加信息，文本会展示在客服人员的多客服客户端
     * @return mixed
     * 返回码	说明
     * 0	成功(no error)/会话已存在(session exsited)
     * 61458	客户正在被其他客服接待(customer accepted by xxx@xxxx)
     * 61459	客服不在线(kf offline)
     */
    public function kfSessionCreate($kfAccount, $openId, $text = '')
    {

        $body = json_encode([
            'kf_account' => strval($kfAccount),
            'openid' => strval($openId),
            'text' => strval($text)
        ]);

        $url = $this->makeUrl($this->apiBaseUrl, 'kfsession/create', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }

    /**
     * @param string $kfAccount 完整客服账号，格式为：账号前缀@公众号微信号
     * @param string $openId 客户openid
     * @param string $text 附加信息，文本会展示在客服人员的多客服客户端
     * @return mixed
     * 返回码	说明
     * 0	成功(no error)/会话已存在(session exsited)
     * 61458	客户正在被其他客服接待(customer accepted by xxx@xxxx)
     */
    public function kfSessionClose($kfAccount, $openId, $text = '')
    {

        $body = json_encode([
            'kf_account' => strval($kfAccount),
            'openid' => strval($openId),
            'text' => strval($text)
        ]);

        $url = $this->makeUrl($this->apiBaseUrl, 'kfsession/close', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }

    /**
     * 获取客户的回话状态
     * @param string $openId
     * @return mixed
     *
     * 参数	描述
     * kf_account	正在接待的客服，为空表示没有人在接待
     * createtime	会话接入的时间
     */
    public function kfSessionGetSession($openId)
    {
        $url = $this->makeUrl($this->apiBaseUrl, 'kfsession/getsession', [
            'access_token' => $this->accessToken,
            'openid' => strval($openId)
        ]);
        $res = json_decode($this->httpGet($url), true);
        return $res;
    }

    /**
     * 获取客服的会话列表
     * @param string $kfAccount 完整客服账号，格式为：账号前缀@公众号微信号，账号前缀最多10个字符，必须是英文或者数字字符
     * @return mixed
     *
     * sessionlist	会话列表
     * sessionlist.openid	客户openid
     * sessionlist.createtime	会话创建时间，UNIX时间戳
     */
    public function kfSessionGetSessionList($kfAccount)
    {
        $url = $this->makeUrl($this->apiBaseUrl, 'kfsession/getsessionlsit', [
            'access_token' => $this->accessToken,
            'kf_account' => strval($kfAccount)
        ]);
        $res = json_decode($this->httpGet($url), true);
        return $res;
    }

    /**
     * 获取未接入会话列表
     * 此接口最多返回最早进入队列的100个未接入会话
     * @return mixed
     *
     * count	未接入会话数量
     * waitcaselist	未接入会话列表，最多返回100条数据
     * waitcaselist.openid	客户openid
     * waitcaselist.kf_account	指定接待的客服，为空表示未指定客服
     * waitcaselist.createtime	用户来访时间，UNIX时间戳
     */
    public function kfSessionGetWaitCase()
    {
        $url = $this->makeUrl($this->apiBaseUrl, 'kfsession/getwaitcase', $this->getParams);
        $res = json_decode($this->httpGet($url), true);
        return $res;
    }

    /**
     * @param int $pageIndex 查询第几页，从1开始
     * @param int $startTime 查询开始时间，UNIX时间戳
     * @param int $endTime 查询结束时间，UNIX时间戳，每次查询不能跨日查询
     * @param int $pageSize 每页大小，每页最多拉取50条
     * @return mixed
     *
     * 参数	说明
     * worker	客服账号
     * openid	用户的标识，对当前公众号唯一
     * opercode	操作ID（会话状态），具体说明见下文
     * time	操作时间，UNIX时间戳
     * text	聊天记录
     *
     * 操作ID(会话状态）定义：
     * ID值	说明
     * 1000	创建未接入会话
     * 1001	接入会话
     * 1002	主动发起会话
     * 1003	转接会话
     * 1004	关闭会话
     * 1005	抢接会话
     * 2001	公众号收到消息
     * 2002	客服发送消息
     * 2003	客服收到消息
     */
    public function msgRecordGetRecord($pageIndex, $startTime, $endTime, $pageSize = 40)
    {
        $body = json_encode([
            'pageindex' => intval($pageIndex),
            'pagesize' => intval($pageSize),
            'starttime' => intval($startTime),
            'endtime' => intval($endTime)
        ]);

        $url = $this->makeUrl($this->apiBaseUrl, 'msgrecord/getrecord', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }



}