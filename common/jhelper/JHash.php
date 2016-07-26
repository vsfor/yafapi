<?php
namespace jhelper;

class JHash
{
    public static function getHash($str)
    {
        return md5($str);
    }

    public static function checkHash($hash, $key)
    {
        return self::getHash($key) === $hash;
    }

    public static function getCsrf($hmStr = null)
    {
        $hmNow = $hmStr ? : date('H:i');
        $csrfStr = \Yaf\Registry::get('config')->app->csrf->key . $hmNow;
        return self::getHash($csrfStr) . '|' . $hmNow;
    }

    public static function checkCsrf()
    {
        if (!\Yaf\Registry::get('config')->app->csrf->check) {
            return true;
        }

        if (!isset($_COOKIE['jeen_auth'])) {
            return false;
        }

        $authArr = explode('|', $_COOKIE['jeen_auth']);
        $hmStr = isset($authArr[1]) ? $authArr[1] : '';
        if (!$hmStr) {
            return false;
        }

        $timeStr = date('Y-m-d') . ' ' . $hmStr . ':00';
        if ((time() - strtotime($timeStr)) > \Yaf\Registry::get('config')->app->csrf->timeout) {
            return false;
        }

        return $_COOKIE['jeen_auth'] == self::getCsrf($hmStr);
    }
    
}