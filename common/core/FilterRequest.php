<?php
namespace core;

class FilterRequest
{
    const checkPass = 0;
    const timeError = 1;
    const ipDeny = 2;
    const userAgentDeny = 3;
    const userIdDeny = 4;

    /**
     * 请求信息过滤  请求时间, IP黑名单, UserAgent黑名单, 用户黑名单
     * //... 可自行添加其他 请求过滤条件
     * @param array $request
     * @return int
     */
    public static function check(array $request = [])
    {
        $config = \Yaf\Registry::get('requestConfig');
        if (isset($config['timeDiffLimit']) && abs(intval($request['timeStamp']) - time()) > $config['timeDiffLimit']) {
            return self::timeError;
        }

        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        if (isset($config['ipBlackList']) && in_array($ip, $config['ipBlackList'])) {
            return self::ipDeny;
        }

        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        if (isset($config['userAgentBlackList']) && in_array($userAgent, $config['userAgentBlackList'])) {
            return self::userAgentDeny;
        }

        $userId = isset($request['userId']) ? $request['userId'] : 0;
        if (isset($config['userIdBlackList']) && in_array($userId, $config['userIdBlackList'])) {
            return self::userIdDeny;
        }

        return self::checkPass;
    } 

}