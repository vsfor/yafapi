<?php
namespace vendor\jeen\wechat\mod;

/**
 * 名称认证失败
 * Class NamingVerifyFail
 * @package vendor\jeen\wechat\mod
 */
class NamingVerifyFail extends Base
{
    public $Event = "naming_verify_fail";
    public $FailTime; //失败发生时间 (整形)，时间戳
    public $FailReason; //认证失败的原因
}