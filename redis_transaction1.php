<?php
// 下面测试redis的事务
$timeout = 3; // 连接超时时间，貌似没什么用，设置了和没设置一样的
$redis = new Redis();
$result = $redis->open('192.168.0.100', 6379, $timeout); // open和connect是一样的。 如果连不上直接抛异常
var_dump($result);

$redis->auth('ouhaixiong');
$redis->select(16);
$key1 = 'code_languge';
$redis->sAdd($key1, 'java');
$redis->sAdd($key1, 'python');
$members = $redis->sMembers($key1);
var_dump($members);

// 下面测试事务的取消
$redis->multi(); // 开始事务
$redis->sAdd($key1, 'php');
$redis->sAdd($key1, 'go');
$members = $redis->sMembers($key1); // 这里返回：object(Redis)#1 (0) {} 因为在开始事务后，所有的操作都只会返回redis对象，直到exec或discard执行
var_dump($members);
$redis->discard(); // 取消事务
$members = $redis->sMembers($key1); // 这里返回的数据还是：java、python
var_dump($members);

// 下面测试延迟事务提交
$redis->watch($key1); // 监控$key1这个键，如果在执行事务的过程中，有程序修改过该键，那么整个事务会执行失败，即都不会执行。  貌似监控多个key需要调用此方法多次
$redis->multi();
$redis->sAdd($key1, 'php');
$redis->sAdd($key1, 'go');
sleep(20); // 延迟执行，睡眠20秒
$boolean = $redis->exec(); // 提交事务。 返回true或false
$members = $redis->sMembers($key1);
var_dump($members);

$redis->sRemove($key1, 'php');
$redis->srem($key1, 'jave');
$redis->sRemove($key1, 'go');
$redis->sRemove($key1, 'js');
$members = $redis->sMembers($key1);
var_dump($members);
// 总结：redis的事务通过watch可以监控对应的键是否有修改过，如果没有修改，事务可以执行成功，否则事务会失败（取消）
// 如果事务中的命令是错误的，比如对不同的数据类型使用了错误的命令，那么事务并不会终止，能执行成功的命令依然会执行

$redis->close();
