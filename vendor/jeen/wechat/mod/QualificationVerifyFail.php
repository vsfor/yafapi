<?php
namespace vendor\jeen\wechat\mod;

/**
 * 资质认证失败
 * Class QualificationVerifyFail
 * @package vendor\jeen\wechat\mod
 */
class QualificationVerifyFail extends Base
{
    public $Event = "qualification_verify_fail";
    public $FailTime; //失败发生时间 (整形)，时间戳
    public $FailReason; //认证失败的原因
}