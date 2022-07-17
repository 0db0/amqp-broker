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

$channel->exchange_declare('direct_logs', AMQPExchangeType::DIRECT, false, false, false);

[$queueName] = $channel->queue_declare('', false, false, true, false);

$severities = array_slice($argv, 1);

if (empty($severities)) {
    file_put_contents('php://stderr', sprintf('Usage: $argv[0] [info] [warning] [error]%s', PHP_EOL));
    exit(1);
}
foreach ($severities as $severity) {
    $channel->queue_bind($queueName, 'direct_logs', $severity);
}

echo '[x] Waiting for logs. To exit press CTRL+C' . PHP_EOL;

$callback = static function(AMQPMessage $msg): void {
    echo sprintf('[x] %s: %s%s', $msg->get('routing_key'), $msg->getBody(), PHP_EOL);
};

$channel->basic_consume($queueName, '',false, true, false, false, $callback);

$shutdownCallback = static function(AMQPChannel $channel, AMQPStreamConnection $connection): void {
    $channel->close();
    $connection->close();
};

register_shutdown_function($shutdownCallback, $channel, $connection);
$channel->consume();