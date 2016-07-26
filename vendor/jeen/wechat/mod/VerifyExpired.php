<?php
namespace vendor\jeen\wechat\mod;

/**
 * 认证过期失效通知
 * Class VerifyExpired
 * @package vendor\jeen\wechat\mod
 */
class VerifyExpired extends Base
{
    public $Event = "verify_expired";
    public $ExpiredTime; //	有效期 (整形)，指的是时间戳，表示已于该时间戳认证过期，需要重新发起微信认证
}