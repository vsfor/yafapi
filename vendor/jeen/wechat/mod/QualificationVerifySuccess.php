<?php
namespace vendor\jeen\wechat\mod;

/**
 * 资质认证成功
 * Class QualificationVerifySuccess
 * @package vendor\jeen\wechat\mod
 */
class QualificationVerifySuccess extends Base
{
    public $Event = "qualification_verify_success";
    public $ExpiredTime; //有效期 (整形)，指的是时间戳，将于该时间戳认证过期
}