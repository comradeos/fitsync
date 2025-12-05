<?php

require __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPIOException;
use PhpAmqpLib\Message\AMQPMessage;

class NotificationConsumer
{
    private string $host = 'rabbitmq';
    private int $port = 5672;
    private string $user = 'fitsync';
    private string $pass = 'fitsync';

    private string $exchange = 'events';
    private string $queue = 'notifier.measurements';
    private string $routingKey = 'measurement.created';

    public function run(): void
    {
        echo "[NOTIFIER] Starting worker...\n";

        while (true) {
            try {
                $this->listen();
            } catch (\Throwable $e) {
                echo "[NOTIFIER] ERROR: {$e->getMessage()}\n";
                echo "[NOTIFIER] Reconnecting in 3 seconds...\n";
                sleep(3);
            }
        }
    }

    private function listen(): void
    {
        echo "[NOTIFIER] Connecting to RabbitMQ...\n";

        $connection = new AMQPStreamConnection(
            $this->host,
            $this->port,
            $this->user,
            $this->pass
        );

        $channel = $connection->channel();

        $channel->exchange_declare(
            $this->exchange,
            'topic',
            false,
            true,
            false
        );

        $channel->queue_declare(
            $this->queue,
            passive: false,
            durable: true,
            exclusive: false,
            auto_delete: false
        );

        $channel->queue_bind(
            $this->queue,
            $this->exchange,
            $this->routingKey
        );

        echo "[NOTIFIER] Listening for {$this->routingKey}...\n";

        $callback = function (AMQPMessage $msg) {
            $body = $msg->body;

            echo "[EVENT] $body\n";

            file_get_contents("http://websocket:9003", false, stream_context_create([
                'http' => [
                    'method'  => 'POST',
                    'header'  => "Content-Type: application/json\r\n",
                    'content' => $body,
                ]
            ]));

            file_put_contents(
                '/var/www/notifier.log',
                date('c') . " $body\n",
                FILE_APPEND
            );
        };

        $channel->basic_consume(
            queue: $this->queue,
            consumer_tag: '',
            no_local: false,
            no_ack: true,
            exclusive: false,
            nowait: false,
            callback: $callback
        );

        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }
}

$worker = new NotificationConsumer();
$worker->run();
