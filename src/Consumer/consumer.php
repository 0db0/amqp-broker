#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exchange\AMQPExchangeType;

error_reporting(E_ALL & ~E_DEPRECATED);

$connection = new AMQPStreamConnection('message-broker', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->exchange_declare('logs', AMQPExchangeType::FANOUT, false, false, false);

echo '[x] Waiting for messages. To exit press CTRL+C' . PHP_EOL;

$callback = static function(AMQPMessage $msg): void {
    echo sprintf('[x] Received %s%s', $msg->getBody(), PHP_EOL);
    sleep(substr_count($msg->getBody(), '.'));
    echo sprintf('[x] Done!%s', PHP_EOL);
//    $msg->ack();
};

[$queueName] = $channel->queue_declare('', false, false, true, false);
$channel->queue_bind($queueName, 'logs');

//$channel->basic_qos(null, 1, null);
$channel->basic_consume($queueName, '',false, true, false, false, $callback);

$shutdownCallback = static function(AMQPChannel $channel, AMQPStreamConnection $connection): void {
    $channel->close();
    $connection->close();
};

register_shutdown_function($shutdownCallback, $channel, $connection);
$channel->consume();