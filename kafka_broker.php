<?php
// 查看服务器元数据（topic/partition/broker）
$conf = new RdKafka\Conf();
$conf->setDrMsgCb(function ($kafka, $message) {
    file_put_contents("./xx.log", var_export($message, true), FILE_APPEND);
});
$conf->setErrorCb(function ($kafka, $err, $reason) {
    printf("Kafka error: %s (reason: %s)\n", rd_kafka_err2str($err), $reason);
});

$conf->set('group.id', 'myConsumerGroup');

$rk = new RdKafka\Consumer($conf);
$rk->addBrokers("127.0.0.1");

$allInfo = $rk->metadata(true, NULL, 60e3); // 这个方法一废弃

$topics = $allInfo->getTopics();

echo rd_kafka_offset_tail(100);
echo "--";

echo count($topics);
echo "--";


foreach ($topics as $topic) {

    $topicName = $topic->getTopic();
    if ($topicName == "__consumer_offsets") {
        continue ;
    }

    $partitions = $topic->getPartitions();
    foreach ($partitions as $partition) {
//        $rf = new ReflectionClass(get_class($partition));
//        foreach ($rf->getMethods() as $f) {
//            var_dump($f);
//        }
//        die();
        $topPartition = new RdKafka\TopicPartition($topicName, $partition->getId());
        echo  "当前的话题：" . ($topPartition->getTopic()) . " - " . $partition->getId() . " - ";
        echo  "offset：" . ($topPartition->getOffset()) . PHP_EOL;
    }
}