<?php

namespace App\Service;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQPublisher
{
    private AMQPStreamConnection $connection;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            'rabbitmq',
            5672,
            'fitsync',
            'fitsync'
        );
    }

    public function publish(string $routingKey, array $payload): void
    {
        $channel = $this->connection->channel();

        // declare exchange
        $channel->exchange_declare(
            'events',
            'topic',
            false,
            true,
            false
        );

        $msg = new AMQPMessage(json_encode($payload), [
            'content_type' => 'application/json',
            'delivery_mode' => 2 // make message persistent
        ]);

        $channel->basic_publish($msg, 'events', $routingKey);

        $channel->close();
    }
}
