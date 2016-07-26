<?php
namespace vendor\jeen\wechat\mod;

/**
 * 年审通知
 * Class AnnualRenew
 * @package vendor\jeen\wechat\mod
 */
class AnnualRenew extends Base
{
    public $Event = "annual_renew"; //事件类型 annual_renew，提醒公众号需要去年审了
    public $ExpiredTime; //	有效期 (整形)，指的是时间戳，将于该时间戳认证过期，需尽快年审
}