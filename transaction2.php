<?php

$dsn = 'mysql:host=127.0.0.1;dbname=test';
$username = 'root';
$password = '123456';
$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => "set names 'utf8'",
);
$pdo = new PDO($dsn, $username, $password, $options);
// $sql = "insert into UserAccount (accountNumber,status) values (" . mt_rand(9000, 88888) . ", 1)";
// $number = $pdo->exec($sql);
// var_dump('新增记录数：' . $number);

$pdo->beginTransaction();
try {
    $id = 60;
    $sql = 'select * from UserAccount where id=' . $id;
    $statement = $pdo->query($sql);
    if ($statement instanceof PDOStatement) {
        $data = $statement->fetch();
        var_dump('查询到的数据：', $data);
    } else {
        echo ('无法找到该记录');
    }
}  catch (Exception $e) {
    $pdo->rollBack();
    var_dump($e->getMessage() . $e->getCode());
}

var_dump(time());