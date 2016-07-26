<?php
namespace vendor\jeen\wechat;

/**
 * 微信小店 @todo
 * Class MpMerchant
 * @package vendor\jeen\wechat
 */
class MpMerchant extends Mp
{
    private static $instance;
    /**
     * @param string $appId
     * @param string $appSecret
     * @param string $token
     * @return MpMerchant
     */
    public static function getInstance($appId='', $appSecret='', $token='')
    {
        $classHash = 'mp_'.md5("$appId:$appSecret:$token");
        if(empty(self::$instance[$classHash])){
            self::$instance[$classHash] = new self($appId,$appSecret,$token);
        }
        return self::$instance[$classHash];
    }

    private $apiBaseUrl = 'https://api.weixin.qq.com/merchant/';


    /**
     * 商品
     *
     * 库存
     *
     * 邮费模板
     *
     * 分组
     *
     * 货架
     *
     * 订单
     *
     * 功能接口
     */

}