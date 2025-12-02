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
            host: 'rabbitmq',
            port: 5672,
            user: 'fitsync',
            password: 'fitsync'
        );
    }

    public function publish(string $queue, array $data): void
    {
        $channel = $this->connection->channel();

        $channel->queue_declare(
            queue: $queue,
            auto_delete: false
        );

        $message = new AMQPMessage(json_encode($data));

        $channel->basic_publish($message, '', $queue);

        $channel->close();
    }
}
