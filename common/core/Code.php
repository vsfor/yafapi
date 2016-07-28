<?php
namespace core;

class Code
{
    //API  Request  Response  Code
    const SuccessCode = 2000; //请求成功

    const FailedCode = 4000; //请求失败
    const ErrorParam = 4011; // 缺少必要参数
    const InvalidParam = 4012; //参数非法

    const BadCode = 0; //返回结果异常 (一般会由于在逻辑中设置了非法的返回码  导致 code 为 0)
    const ExceptionCode = 5000; //执行异常

    const InvalidRequest = 5011; //请求非法
    const ExpireRequest = 5012; //请求过期

    const InvalidApiToken = 5021; //ApiToken非法
    const ExpireApiToken = 5022; //ApiToken过期

    const InvalidUserToken = 5031; //UserToken非法
    const ExpireUserToken = 5032; //UserToken过期


    public static function getCodeDes($code = 0)
    {
        switch ($code) {
            case self::SuccessCode: return 'ok';

            case self::FailedCode: return 'error';
            case self::ErrorParam: return '缺少必要参数';
            case self::InvalidParam: return '参数错误';

            case self::ExceptionCode: return '执行异常';

            case self::InvalidRequest: return '请求非法';
            case self::ExpireRequest: return '请求过期';

            case self::InvalidApiToken: return '无效请求';
            case self::ExpireApiToken: return '无效请求';

            case self::InvalidUserToken: return '无效会话';
            case self::ExpireUserToken: return '无效会话';

            default: return '未知错误';
        }
    }
}