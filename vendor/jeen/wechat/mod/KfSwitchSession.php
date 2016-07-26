<?php
namespace vendor\jeen\wechat\mod;

/**
 * 客服消息推送  转接会话
 * Class KfSwitchSession
 * @package vendor\jeen\wechat\mod
 */
class KfSwitchSession extends Base
{
    public $Event = 'kf_switch_session';

    public $FromKfAccount;

    public $ToKfAccount;
}