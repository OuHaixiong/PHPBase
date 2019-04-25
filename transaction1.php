<?php

$dsn = 'mysql:host=127.0.0.1;dbname=test';
$username = 'root';
$password = '123456';
$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => "set names 'utf8'",
);
$pdo = new PDO($dsn, $username, $password, $options);
$pdo->beginTransaction();
// $isolationLevel = 'repeatable read';
$isolationLevel = 'read uncommitted';
// $isolationLevel = 'read committed';
// $isolationLevel = 'SERIALIZABLE';
$sqlIsolationLevel = 'set transaction isolation level ' . $isolationLevel;

// try {//var_dump($sqlIsolationLevel);exit;
//     $pdo->exec($sqlIsolationLevel);
    
//     $sql = 'select count(*) from UserAccount';
//     $statement = $pdo->query($sql);
//     if ($statement instanceof PDOStatement) {
//         $count = $statement->fetchColumn(0);
//         var_dump('第一次查询总记录数：' . $count);
        
//         sleep(15);
        
//         $sql = 'select count(id) from UserAccount';
//         $statement = $pdo->query($sql);
//         $count = $statement->fetchColumn();
//         var_dump('第二次查询总记录数：' . $count);
//     } else {
//         throw new Exception('执行sql失败', 1001);
//     }
//     throw new Exception('主动抛异常', 1002);
//     $pdo->commit();
// } catch (Exception $e) {
//     $pdo->rollBack();
//     var_dump($e->getMessage() . $e->getCode());
//     var_dump(time());
// }

try {//var_dump($sqlIsolationLevel);exit;
    $pdo->exec($sqlIsolationLevel);

    $id = 60;
    $updateSql = 'update UserAccount set accountNumber=444 where id=' . $id;
    $number = $pdo->exec($updateSql);
    var_dump('更新记录数：' . $number);
    
    $sql = 'select * from UserAccount where id=' . $id;
    $statement = $pdo->query($sql);
    if ($statement instanceof PDOStatement) {
        $data = $statement->fetch();
        var_dump('查询未提交的更新：', $data);

        sleep(15);

    } else {
        throw new Exception('执行查询sql失败', 1001);
    }
    throw new Exception('主动抛异常', 1002);
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    var_dump($e->getMessage() . $e->getCode());
    var_dump(time());
}