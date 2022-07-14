<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

//$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$connection = new AMQPStreamConnection('message-broker', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->queue_declare('hello_queue', false, false, false, false);

$message= new AMQPMessage('FOO BAR');
$channel->basic_publish($message, '', 'hello_queue');

echo '[x] Sent FOO-BAR message' . PHP_EOL;

$channel->close();
$connection->close();
