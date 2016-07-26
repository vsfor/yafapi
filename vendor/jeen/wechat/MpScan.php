<?php
namespace vendor\jeen\wechat;

/**
 * 微信扫一扫 @todo
 * Class MpScan
 * @package vendor\jeen\wechat
 */
class MpScan extends Mp
{
    private static $instance;
    /**
     * @param string $appId
     * @param string $appSecret
     * @param string $token
     * @return MpScan
     */
    public static function getInstance($appId='', $appSecret='', $token='')
    {
        $classHash = 'mp_'.md5("$appId:$appSecret:$token");
        if(empty(self::$instance[$classHash])){
            self::$instance[$classHash] = new self($appId,$appSecret,$token);
        }
        return self::$instance[$classHash];
    }

    private $apiBaseUrl = 'https://api.weixin.qq.com/scan/';


}