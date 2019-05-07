<?php 

//(($param1 == $param2) &&($param3 == $param4)) 
// param1 => 10001  (客户代码，从用户传入的需计费单据中获取)
// param2 => 10001  (广州联邦，从我们系统的客户资料中选出来的) 
// param3 => 201    (申报地口岸，从用户传入的需计费单据中获取)
// param4 => 201    (广州机场，从我们系统的基本数据申报地口岸选出来的)
$param1 = '1001';
$param2 = '1001';
$param3 = '201';
$param4 = '201';
include 't1.php';
var_dump($boolean);
$param3 = '301';
include 't1.php';
var_dump($boolean);


// 下面演示计算总价
// [@数量]*[@单价]
$param1 = 80; // 数量
$param2 = 5;  // 单价
include 't2.php';
var_dump($total);