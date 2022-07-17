<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exchange\AMQPExchangeType;

error_reporting(E_ALL & ~E_DEPRECATED);

$severity = $argv[1] ?? 'info';

$content = 'FOO BAR';

if ($argc > 2) {
    $content = implode(' ', array_slice($argv, 1));
}

$connection = new AMQPStreamConnection('message-broker', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->exchange_declare('direct_logs', AMQPExchangeType::DIRECT, false, false, false);

$message= new AMQPMessage($content);
$channel->basic_publish($message, 'direct_logs', $severity);

echo sprintf('[x] Sent %s%s', $content, PHP_EOL);

$channel->close();
$connection->close();
