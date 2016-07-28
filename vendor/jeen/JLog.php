<?php
namespace vendor\jeen;
use jhelper\JFile;

/**
 * Class JLog
 * @package vendor\jeen
 */
class JLog
{
    /* */

//    const SEASLOG_DEBUG = "debug";
//    const SEASLOG_INFO = "info";
//    const SEASLOG_NOTICE = "notice";
//    const SEASLOG_WARNING = "warning";
//    const SEASLOG_ERROR = "error";
//    const SEASLOG_CRITICAL = "critical";
//    const SEASLOG_ALERT = "alert";
//    const SEASLOG_EMERGENCY = "emergency";

    private static $log_extend = 'jeen'; //jeen  seaslog
    private static $log_path = APP_PATH . '/var/log';
    private static $log_dir = 'default';

    private static function jeenLog($level, $message, array $content = [], $module = '')
    {
        $path = self::$log_path . DS . self::$log_dir . DS . ($module ? $module . DS : '');
        JFile::createDirectory($path, 0777); //目录不存在 则创建目录 并开放权限

        $file = $path . $level . date("_Y-m-d") . '.log';
        JFile::touchFile($file, 0777);//文件不存在 则创建文件 并开放权限

        if ($content) {
            $message = str_replace(array_keys($content),$content,$message);
        }
        $msg =  $level 
            . ' | ' . posix_getpid()
            . ' | ' . microtime(true)
            . date(' | Y-m-d H:i:s | ') 
            . $message 
            . PHP_EOL;
        return error_log($msg, 3, $file);
    }

    /**
     * 通用日志方法
     * @param string $level
     * @param string $message
     * @param array $content
     * @param string $module
     */
    public static function log($level,$message,array $content = [],$module = '')
    {
        if (self::$log_extend == 'seaslog') {
            \SeasLog::log($level,$message,$content,$module);
        } else {
            self::jeenLog($level,$message,$content,$module);
        }
    }

    /**
     * 记录debug日志
     * @param string $message
     * @param array $content
     * @param string $module
     */
    public static function debug($message,array $content = [],$module = '')
    {
        if (self::$log_extend == 'seaslog') {
            \SeasLog::debug($message,$content,$module);
        } else {
            self::jeenLog('debug',$message,$content,$module);
        }
    }

    /**
     * 记录info日志
     * @param string $message
     * @param array $content
     * @param string $module
     */
    public static function info($message,array $content = [],$module = '')
    {
        if (self::$log_extend == 'seaslog') {
            \SeasLog::info($message,$content,$module);
        } else {
            self::jeenLog('info',$message,$content,$module);
        }
    }

    /**
     * 记录notice日志
     * @param string $message
     * @param array $content
     * @param string $module
     */
    public static function notice($message,array $content = [],$module = '')
    {
        if (self::$log_extend == 'seaslog') {
            \SeasLog::notice($message,$content,$module);
        } else {
            self::jeenLog('notice',$message,$content,$module);
        }
    }

    /**
     * 记录warning日志
     * @param string $message
     * @param array $content
     * @param string $module
     */
    public static function warning($message,array $content = [],$module = '')
    {
        if (self::$log_extend == 'seaslog') {
            \SeasLog::warning($message,$content,$module);
        } else {
            self::jeenLog('warning',$message,$content,$module);
        }
    }

    /**
     * 记录error日志
     * @param string $message
     * @param array $content
     * @param string $module
     */
    public static function error($message,array $content = [],$module = '')
    {
        if (self::$log_extend == 'seaslog') {
            \SeasLog::error($message, $content, $module);
        } else {
            self::jeenLog('error',$message,$content,$module);
        }
    }

    /**
     * 记录critical日志
     * @param string $message
     * @param array $content
     * @param string $module
     */
    public static function critical($message,array $content = [],$module = '')
    {
        if (self::$log_extend == 'seaslog') {
            \SeasLog::critical($message,$content,$module);
        } else {
            self::jeenLog('critical',$message,$content,$module);
        }
    }

    /**
     * 记录alert日志
     * @param string $message
     * @param array $content
     * @param string $module
     */
    public static function alert($message,array $content = [],$module = '')
    {
        if (self::$log_extend == 'seaslog') {
            \SeasLog::alert($message,$content,$module);
        } else {
            self::jeenLog('alert',$message,$content,$module);
        }
    }

    /**
     * 记录emergency日志
     * @param string $message
     * @param array $content
     * @param string $module
     */
    public static function emergency($message,array $content = [],$module = '')
    {
        if (self::$log_extend == 'seaslog') {
            \SeasLog::emergency($message,$content,$module);
        } else {
            self::jeenLog('emergency',$message,$content,$module);
        }
    }

    /**
     * 设置basePath
     * @param string $path
     * @return bool
     */
    public static function setBasePath($path)
    {
        if (self::$log_extend == 'seaslog') {
            return \SeasLog::setBasePath($path);
        } else {
            self::$log_path = $path;
            return true;
        }
    }

    /**
     * 获取basePath
     * @return string
     */
    public static function getBasePath()
    {
        if (self::$log_extend == 'seaslog') {
            return \SeasLog::getBasePath();
        } else {
            return self::$log_path;
        }
    }

    /**
     * 设置模块目录
     * @param string $module
     * @return bool
     */
    public static function setLogger($module)
    {
        if (self::$log_extend == 'seaslog') {
            return \SeasLog::setLogger($module);
        } else {
            self::$log_dir = $module;
            return true;
        }
    }

    /**
     * 获取最后一次设置的模块目录
     * @return string
     */
    public static function getLastLogger()
    {
        if (self::$log_extend == 'seaslog') {
            return \SeasLog::getLastLogger();
        } else {
            return self::$log_dir;
        }
    }

    /**
     * 统计所有类型（或单个类型）行数
     * @param string $level
     * @param string $log_path
     * @param null $key_word
     * @return array | long
     */
    public static function analyzerCount($level = 'all',$log_path = '*',$key_word = NULL)
    {
        if (self::$log_extend == 'seaslog') {
            return \SeasLog::analyzerCount($level,$log_path,$key_word);
        } else {
            return false;
        }
    }

    /**
     * 以数组形式，快速取出某类型log的各行详情
     *
     * @param string $level
     * @param string $log_path
     * @param null   $key_word
     * @param int    $start
     * @param int    $limit
     * @return array
     */
    public static function analyzerDetail($level = SEASLOG_INFO, $log_path = '*', $key_word = NULL, $start = 1, $limit = 20)
    {
        if (self::$log_extend == 'seaslog') {
            return \SeasLog::analyzerDetail($level,$log_path,$key_word,$start,$limit);
        } else {
            return false;
        }
    }


    /**
     * 获得当前日志buffer中的内容
     * @return array
     */
    public static function getBuffer()
    {
        if (self::$log_extend == 'seaslog') {
            return \SeasLog::getBuffer();
        } else {
            return false;
        }
    }

    /**
     * 将buffer中的日志立刻刷到硬盘
     * @return bool
     */
    public static function flushBuffer()
    {
        if (self::$log_extend == 'seaslog') {
            return \SeasLog::flushBuffer();
        } else {
            return false;
        }
    }


}