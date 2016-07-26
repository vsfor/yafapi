<?php
namespace vendor\jeen\wechat\mod;

/**
 * 客服消息推送  接入会话
 * Class KfCreateSession
 * @package vendor\jeen\wechat\mod
 */
class KfCreateSession extends Base
{
    public $Event = 'kf_create_session';

    public $KfAccount;
}