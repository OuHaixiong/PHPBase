<?php
$filePath = '/data/www/PHPBase/data/发票.pdf';
$filename = preg_replace('/^.+[\\\\\\/]/', '', $filePath); 
var_dump($filename);


/**
 * 生成随机的字符串
 * @param integer $length 需要的字符串长度
 * @return string
 */
function random($length = 6) {
    $hash = '';
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
    mt_srand((double)microtime()*1000000); // 播下一个更好的随机数发生器种子
    //自 PHP 4.2.0 起，不再需要用 srand() 或 mt_srand() 给随机数发生器播种 ，因为现在是由系统自动完成的。
    $len = strlen($chars)-1;
    for ($i=0; $i<$length; $i++) {
        $hash .= $chars[mt_rand(0, $len)];
    }
    return $hash;
}

$pwd = sha1('9bf96bfa7'.sha1('9bf96bfa7'.sha1('5655555')));
var_dump($pwd);

var_dump(random(32));

$string = 'B欧阳海雄Bear-Ou';

function reversal($str) {
    $len = mb_strlen($str, 'UTF-8');
    $temp = '';
    for ($i=$len-1; $i>=0; $i--) {
        $temp .= mb_substr($str, $i, 1, 'UTF-8');
    }
    return $temp;
}

$reversalString = reversal($string);
var_dump($reversalString);
