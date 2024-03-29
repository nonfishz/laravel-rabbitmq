<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Nonfishz\RabbitMQ\RabbitMQ;
use Nonfishz\RabbitMQ\RabbitMQExchange;
use Nonfishz\RabbitMQ\RabbitMQQueue;


$exchange = new RabbitMQExchange(
    'tmp_dead_exchange',
    'topic',
    true, // durable
    false  // auto delete
);

$queue = new RabbitMQQueue(
    'tmp_dead_queue',
    true, // durable
    false, // exclusive
    false, // auto delete
    'tmp_dead_queue'
);

$config = array(
    "connections"=>array(
        "default"=>array(
            'host' => '47.103.78.179',
            'port' => '5672',
            'username' => 'admin',
            'password' => '123456',
            'vhost' => '/',
            "heartbeat_interval" => 30,
        )
    )
);
$mq = new RabbitMQ($config);
// 创建一个消息消费器
$consumer = $mq->createConsumer(
    $exchange,
    $queue,
    'default',        // connection name
    true);            // 启用心跳

$consumer->setNetworkRecovery(true);
$consumer->setTopologyRecovery(true);

// 设置消费
$consumer->consume(
    false,  // no_ack
    false,  // exclusive
    function ($message) {
        process_message($message);
    }
);
// 开始消费，这句语句会 block 住
// 同时消费器内部已经针对连接错误进行处理，会自动重连
$consumer->blockingConsume();

/**
 * 业务处理
 * @param $message
 * @throws \Exception
 */
function process_message($message)
{
    $payload = json_decode($message->body, true);
    print_r($payload);
    # 通知MQ
    $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
}
