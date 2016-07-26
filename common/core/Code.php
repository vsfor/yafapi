<?php
namespace core;

class Code
{
    //API  Request  Response  Code
    const BadCode = 0; //返回结果异常 (一般会由于在逻辑中设置了非法的返回码  导致 code 为 0)
    const ExceptionCode = 900; //执行异常
    const BadRequest = 911; //非法请求
    const ExpireRequest = 912; //过期请求
    const InvalidApiToken = 950; //ApiToken非法
    const ExpireApiToken = 951; //ApiToken过期

    const InvalidUserToken = 960; //UserToken非法
    const ExpireUserToken = 961; //UserToken过期

    const InvalidSessionID = 970; //SessionID 非法
    const ExpireSessionID = 971; //SessionID 过期

    const SuccessCode = 1000; //请求成功
    const EmptyCode = 1001; //请求成功  结果为空
    //弱提示
    const FailedCode = 2000; //请求失败
    const ErrorParam = 2001; // 参数错误 (缺少必要参数)
    const InvalidParam = 2002; //参数非法
    const BadParam = 2003; //参数非法  并有返回错误提示
    //强提示
    const ForceErrorCode = 2010;//请求失败 - 包含错误提示

    public static function getCodeDes($code = 0)
    {
        switch ($code) {
            case 1000: return 'ok';
            case 2000: return '失败';
            case 2001: return '缺少必要参数';
            case 2002: return '参数非法';
            case 2003: return '参数错误';
            case 1001: return '查询记录为空';
            case 950: return '非法请求';
            case 951: return '非法请求';
            case 960: return '无效请求';
            case 961: return '无效请求';
            case 970: return '无效会话';
            case 971: return '无效会话';
            case 912: return '请求过期';
            case 911: return '请求非法';
            case 900: return '执行异常';
            default: return '未知错误';
        }
    }
}