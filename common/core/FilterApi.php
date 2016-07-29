<?php
namespace core;

class FilterApi
{
    const checkPass = 0;
    const apiNotFound = 1;
    const apiClosed = 2;
    const dateCheckFailed = 3;
    const versionCheckFailed = 4;
    const rateLimited = 5;
    const levelLimited = 6;
    const requestMethodError = 7;

    /**
     * api 请求过滤, 状态,时间,接口降级,请求方式,版本,频次 ...
     * //... 可自行补充其他过滤规则
     * @param string $api 请求的API名称  module_controller_action 构成
     * @param array $request
     * @return int
     */
    public static function check(string $api,array $request = [])
    {
        $config = \Yaf\Registry::get('apiConfig');
        //检测api是否存在
        if (!isset($config[$api])) {
            return self::apiNotFound;
        }
        $apiConfig = $config[$api];
        unset($config);

        //检测api是否可用
        if (!isset($apiConfig['status']) || !$apiConfig['status']) {
            return self::apiClosed;
        }

        //检测请求时间api是否开放
        if (isset($apiConfig['date']) && !self::checkDate($apiConfig['date'])) {
            return self::dateCheckFailed;
        }

        //检测当前应用运行级别下,接口是否可用
        //todo 必须在入口文件中定义  J_LEVEL 
        if (isset($apiConfig['level']) && $apiConfig['level'] < J_LEVEL) {
            return self::levelLimited;
        }
        
        //检测请求方式
        if (isset($apiConfig['method']) && $apiConfig['method'] && !in_array($request['__method'], $apiConfig['method'])) {
            return self::requestMethodError;
        }
        
        //检测版本是否有效
        if (isset($apiConfig['version']) && !self::checkVersion($apiConfig['version'], $request['appType'], $request['appVersion'])) {
            return self::versionCheckFailed;
        }

        //检测访问频次
        if (isset($apiConfig['rateLimit'])) {
            if (isset($request['userId']) && $request['userId']) {
                $uniqueId = $request['userId'];
            } else {
                $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
                $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
                $uniqueId = md5($request['systemMAC'].'|'.$request['systemIMEI'].'|'.$request['systemIDFA'].":$ip:$userAgent");
            }
            if (self::checkRateLimit($apiConfig['rateLimit'], $api, $uniqueId)) {
                return self::rateLimited;
            }
        }

        return self::checkPass;
    }

    /**
     * 检测接口访问时间, 以服务端时间为准
     * @param array $dateConfig
     * @return bool
     */
    public static function checkDate(array $dateConfig)
    {
        $requestTime = time();
        if (isset($dateConfig['open'], $dateConfig['close'])) {
            $openTime = strtotime($dateConfig['open']);
            $closeTime = strtotime($dateConfig['close']);
            if ($openTime > $closeTime) {
                $openTime -= 86400;
            }
            if ($requestTime < $openTime) {
                return false;
            }
            if ($requestTime > $closeTime) {
                return false;
            }
        } else if (isset($dateConfig['open'])) {
            $openTime = strtotime($dateConfig['open']);
            if ($requestTime < $openTime) {
                return false;
            }
        } else if (isset($dateConfig['close'])) {
            $closeTime = strtotime($dateConfig['close']);
            if ($requestTime > $closeTime) {
                return false;
            }
        } else { //此处标识设置异常
            return false;
        }
        return true;
    }

    /**
     * 检测接口版本
     * @param array $versionConfig
     * @param int $appType
     * @param string $appVersion
     * @return bool
     */
    public static function checkVersion(array $versionConfig,int $appType,string $appVersion)
    {
        switch ($appType) {
            case 1: //android
            {
                if (isset($versionConfig['androidMin']) && version_compare($versionConfig['androidMin'], $appVersion) > 0) {
                    return false;
                }
                if (isset($versionConfig['androidMax']) && version_compare($appVersion, $versionConfig['androidMax']) > 0) {
                    return false;
                }
            } break;
            case 2: //ios
            {
                if (isset($versionConfig['iosMin']) && version_compare($versionConfig['iosMin'], $appVersion) > 0) {
                    return false;
                }
                if (isset($versionConfig['iosMax']) && version_compare($appVersion, $versionConfig['iosMax']) > 0) {
                    return false;
                }
            } break;
            case 3: //web
            {
                if (isset($versionConfig['webMin']) && version_compare($versionConfig['webMin'], $appVersion) > 0) {
                    return false;
                }
                if (isset($versionConfig['webMax']) && version_compare($appVersion, $versionConfig['webMax']) > 0) {
                    return false;
                }
            } break;
            default: //可定义其他规则
                return false; break;
        }
        return true;
    }

    /**
     * 检测接口请求频次
     * @param array $rateLimitConfig
     * @param string $api
     * @param string $uniqueId
     * @return bool
     * @throws \Exception
     */
    public static function checkRateLimit(array $rateLimitConfig,string $api,string $uniqueId)
    {
        $cache = JRedis::getInstance();
        $key = "ApiRateLimit:".md5("$api:$uniqueId");
        $cache->lPush($key, time());
        $cache->expire($key, ($rateLimitConfig['time'] * 2)); //保留两个周期内的数据
        $minTime = time() - $rateLimitConfig['time'];
        $checkTime = $cache->lIndex($key, $rateLimitConfig['max']);
        if (!$checkTime || $checkTime < $minTime) {
            return true;
        }
        return false;
    }

}