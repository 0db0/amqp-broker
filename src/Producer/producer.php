<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exchange\AMQPExchangeType;

error_reporting(E_ALL & ~E_DEPRECATED);

$content = 'FOO BAR';

if ($argc > 1) {
    $content = implode(' ', array_slice($argv, 1));
}

$connection = new AMQPStreamConnection('message-broker', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->exchange_declare('logs', AMQPExchangeType::FANOUT, false, false, false);

[$queueName] = $channel->queue_declare('', false, false, true, false);

$channel->exchange_bind($queueName, 'logs');

$message= new AMQPMessage($content);
$channel->basic_publish($message, '', 'hello_queue');

echo sprintf('[x] Sent %s%s', $content, PHP_EOL);

$shutdownCallback = static function(AMQPChannel $channel, AMQPStreamConnection $connection): void {
    $channel->close();
    $connection->close();
};

register_shutdown_function($shutdownCallback, $channel, $connection);

$channel->consume();
