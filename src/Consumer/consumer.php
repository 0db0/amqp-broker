#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

error_reporting(E_ALL & ~E_DEPRECATED);

$connection = new AMQPStreamConnection('message-broker', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('hello_queue', false, false, false, false);

echo '[x] Waiting for messages. To exit press CTRL+C' . PHP_EOL;

$callback = static function(AMQPMessage $msg): void {
    echo sprintf('[x] Received %s%s', $msg->body, PHP_EOL);
};

$channel->basic_consume('hello_queue', '',false, true, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();