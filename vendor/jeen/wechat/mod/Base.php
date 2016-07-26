<?php
namespace vendor\jeen\wechat\mod;

class Base
{
    public $ToUserName;//接收方账号|开发者微信号
    public $FromUserName;//发送方账号OpenId   系统推送时为系统账号
    public $CreateTime;//消息创建时间
    public $MsgType;//消息类型
    public $Event;//事件类型
}