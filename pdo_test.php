<?php

$config = array( // 主数据库设置
    'dsn' => 'mysql:host=112.74.107.123;dbname=ck_user',
    'emulatePrepare' => true,
    'username' => 'root',
    'password' => 'cukke.com8877169724266353cukke.com',
);
$driverOptions = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'");
$pdo = new PDO($config['dsn'], $config['username'], $config['password'], $driverOptions);
$queryWhere = array(
    'email' => '20643641@163.com'
);
$where = '';
if (is_array($queryWhere)) {
    foreach ($queryWhere as $k=>$v) {
        $queryWhere[$k] = "`{$k}`={$pdo->quote($v)}";
    }
    $where = implode(' AND ', $queryWhere);
}
$sql = "SELECT * FROM `ck_user`";
if (strlen($where) > 0) {
    $sql .= " WHERE {$where}";
}
$statement = $pdo->query($sql);
$data = null;
if ($statement instanceof PDOStatement) {
    $data = $statement->fetchObject();
}
var_dump($data);



