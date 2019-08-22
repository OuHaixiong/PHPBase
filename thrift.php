<?php
// header('Content-Type: application/x-thrift');
error_reporting(E_ALL);
ini_set('display_errors', 'on');
ini_set('error_reporting', E_ALL);

// define('THRIFT_ROOT', '/usr/local/thrift/lib/php/src');
define('THRIFT_ROOT', __DIR__ . '/thrift');
$GLOBALS['THRIFT_ROOT'] = THRIFT_ROOT . '/src';

/**
 * Init Autloader
 */

require_once THRIFT_ROOT . '/lib/ClassLoader/ThriftClassLoader.php';
require_once THRIFT_ROOT . '/src/Thrift.php';
require_once THRIFT_ROOT . '/lib/Transport/TTransport.php';
require_once THRIFT_ROOT . '/lib/Transport/TSocket.php';
require_once THRIFT_ROOT . '/lib/Transport/TBufferedTransport.php';
require_once THRIFT_ROOT . '/lib/Protocol/TProtocol.php';
require_once THRIFT_ROOT . '/lib/Protocol/TBinaryProtocol.php';
require_once THRIFT_ROOT . '/src/packages/hadoopfs/ThriftHadoopFileSystem.php';

$classLoader = new Thrift\ClassLoader\ThriftClassLoader();
$classLoader->registerNamespace('Thrift', THRIFT_ROOT . '/lib');
$classLoader->register();

$socket = new \Thrift\Transport\TSocket('127.0.0.1', 9099); // var_dump($socket);exit;
$socket->setSendTimeout(10000);
$socket->setRecvTimeout(20000);
$socket->setDebug(true);

$transport = new \Thrift\Transport\TBufferedTransport($socket);
// $transport = new \Thrift\Transport\TSocketPool($socket);
// $protocol = new \Thrift\Protocol\TBinaryProtocol($transport);
$protocol = new \Thrift\Protocol\TBinaryProtocolAccelerated($transport);



$client = new \ThriftHadoopFileSystemClient($protocol);
$transport->open();
// try {

    #$pathname = new Pathname(array('pathname' => '/user/hadoop/dir4test'));
    #$fp = $client->open($pathname);
    #var_dump($client->stat($pathname));
    #var_dump($client->read($fp, 0, 1024));
    
    $pathname = new Pathname(array('pathname' => '/user/hadoop/thrift'));
//     $client->mkdirs($pathname);
    if (!$client->exists($pathname)) {
        $client->mkdirs($pathname);
        print('Created dir : ' . $pathname->pathname);
        var_dump($client->stat($pathname));
    }
// } catch (Exception $e) {
//     print_r($e);
// }
$transport->close();
