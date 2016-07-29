<?php //脚本代码测试
function ipInNetwork($ip, $network)
{
    $s = explode('/', $network);
    if (!isset($s[1])) {
        return $ip == $network;
    }
    $mask = str_pad(str_repeat('1',$s[1]), 32, '0', STR_PAD_RIGHT);

    $sIp = decbin(ip2long($s[0]));
    $sNet = ($sIp & $mask);

    $cIp = decbin(ip2long($ip));
    $cNet = ($cIp & $mask);

    return $cNet == $sNet;
}
var_dump(ipInNetwork('127.0','127.1/16'));

$t = "01.2";
if (is_numeric($t)) {
    var_dump($t);
}
var_dump(is_numeric("01.2"));