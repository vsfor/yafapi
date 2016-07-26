<?php
namespace vendor\jeen\wechat\mod;

/**
 * 用户地理位置推送
 * Class Location
 * @package vendor\jeen\wechat\mod
 */
class Location extends Base
{
    public $Event = "LOCATION";
    public $Latitude; //地理位置纬度
    public $Longitude; //地理位置经度
    public $Precision; //地理位置精度
}