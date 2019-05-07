<?php
// 配合redis_transaction1.php测试redis事务
$timeout = 3; // 连接超时时间，貌似没什么用，设置了和没设置一样的
$redis = new Redis();
$result = $redis->connect('192.168.0.100', 6379, $timeout); // open和connect是一样的。 如果连不上直接抛异常
var_dump($result);

$redis->auth('ouhaixiong');
$redis->select(16);
$key1 = 'code_languge';
$redis->sAdd($key1, 'js');
$members = $redis->sMembers($key1);
var_dump($members);exit;