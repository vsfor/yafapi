<?php
namespace vendor\jeen\wechat;

/**
 * 数据统计 @todo
 * Class MpDataCube
 * @package vendor\jeen\wechat
 */
class MpDataCube extends Mp
{
    private static $instance;
    /**
     * @param string $appId
     * @param string $appSecret
     * @param string $token
     * @return MpDataCube
     */
    public static function getInstance($appId='', $appSecret='', $token='')
    {
        $classHash = 'mp_'.md5("$appId:$appSecret:$token");
        if(empty(self::$instance[$classHash])){
            self::$instance[$classHash] = new self($appId,$appSecret,$token);
        }
        return self::$instance[$classHash];
    }

    private $apiBaseUrl = 'https://api.weixin.qq.com/datacube/';


    //用户分析

    //图文分析

    //消息分析

    //接口分析

}