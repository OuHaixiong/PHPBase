<?php
// 验证

$s = '100e4';
var_dump(ctype_digit($s)); // false 。  验证是否为整数数字型的字符串; 如果是integer型的话，返回为false