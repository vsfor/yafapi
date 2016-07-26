<?php
function j_autoLoader($class)
{
    $classFile = APP_PATH . DS . str_replace( '\\', DS, $class) . '.php';
    Yaf\Loader::import($classFile);
}

spl_autoload_register('j_autoLoader', true, true);


if (!function_exists('receiveJapi')) {
    function receiveJapi($retval, $callinfo) {
        \vendor\jeen\JLog::debug('retval:'.json_encode($retval).'|callinfo:'.json_encode($callinfo), [], 'japi/callback');
    }
}