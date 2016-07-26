<?php
namespace vendor\jeen\wechat\mod;

/**
 * 消息转发到客服
 * Class TransferCustomerService
 * @package vendor\jeen\wechat\mod
 */
class TransferCustomerService extends Base
{
    public $MsgType = 'transfer_customer_service';

    /**
     * 转发给指定客服时  返回体许指定 客服账号
     * @var $TransInfo
     * [
     *     KfAccount : ""
     * ]
     */
    public $TransInfo;


}