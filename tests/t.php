<?php //脚本代码测试

$t = "01.2";
if (is_numeric($t)) {
    var_dump($t);
}
var_dump(is_numeric("01.2"));