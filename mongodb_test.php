<?php
ini_set('display_errors', 'On');
ini_set('error_reporting', E_ALL); // 低于5.4版 error_reporting(E_ALL | E_STRICT);
error_reporting(E_ALL);
// $driver = 'mongodb://root:nrYoxSqC@192.167.11.100:27017'; // mongodb://[username:password@]host1[:port1][,host2[:port2:],…]/database
// 开启验证后会有问题，后续跟进为什么？ auth=true  （如果不需要密码，你传入了密码就会报Authentication failed错误）
$driver = 'mongodb://192.167.11.100:27017';
$dbName = 'mydb'; // 数据库
$collect = '.products'; // 集合
$manger = new MongoDB\Driver\Manager($driver); // 连接到mongodb，新版的需要这样写
$filter = [];
$options = [];
$query = new MongoDB\Driver\Query($filter, $options);
$cursor = $manger->executeQuery($dbName.$collect, $query);

// 迭代显示文档标题
foreach ($cursor as $document) {
    //echo $document["title"] . "\n";
    var_dump($document);exit;
}