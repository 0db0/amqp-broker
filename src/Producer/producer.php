<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exchange\AMQPExchangeType;

error_reporting(E_ALL & ~E_DEPRECATED);

$routingKey = $argv[1] ?? 'anonymous.info';

$content = 'FOO BAR';

if ($argc > 2) {
    $content = implode(' ', array_slice($argv, 2));
}

$connection = new AMQPStreamConnection('message-broker', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->exchange_declare('topic_logs', AMQPExchangeType::TOPIC, false, false, false);

$message= new AMQPMessage($content);
$channel->basic_publish($message, 'topic_logs', $routingKey);

echo sprintf('[x] Sent %s: %s%s', $routingKey, $content, PHP_EOL);

$channel->close();
$connection->close();
