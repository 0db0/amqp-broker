<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

error_reporting(E_ALL & ~E_DEPRECATED);

$content = 'FOO BAR';

if ($argc > 1) {
    $content = implode(' ', array_slice($argv, 1));
}

$connection = new AMQPStreamConnection('message-broker', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->queue_declare('hello_queue', false, false, false, false);

$message= new AMQPMessage($content);
$channel->basic_publish($message, '', 'hello_queue');

echo sprintf('[x] Sent %s%s', $content, PHP_EOL);

$channel->close();
$connection->close();
