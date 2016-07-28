<?php //脚本代码测试
var_dump(version_compare('1.0.2.1','1.0.2.1'));
$t = "01.2";
if (is_numeric($t)) {
    var_dump($t);
}
var_dump(is_numeric("01.2"));