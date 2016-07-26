<?php
namespace vendor\jeen\wechat\mod;

/**
 * 名称认证成功
 * Class QualificationVerifySuccess
 * @package vendor\jeen\wechat\mod
 */
class NamingVerifySuccess extends Base
{
    public $Event = "naming_verify_success";
    public $ExpiredTime; //有效期 (整形)，指的是时间戳，将于该时间戳认证过期
}