<?php
namespace vendor\jeen\wechat\mod;

/**
 * 客服消息推送  关闭会话
 * Class KfCloseSession
 * @package vendor\jeen\wechat\mod
 */
class KfCloseSession extends Base
{
    public $Event = 'kf_close_session';

    public $KfAccount;
}