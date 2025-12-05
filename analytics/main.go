package main

import (
    "context"
    "encoding/json"
    "fmt"
    "log"

    "github.com/rabbitmq/amqp091-go"
    "github.com/redis/go-redis/v9"
)

type MeasurementEvent struct {
    Id        int     `json:"id"`
    UserId    int     `json:"user_id"`
    DeviceId  int     `json:"device_id"`
    Type      string  `json:"type"`
    Value     float64 `json:"value"`
    Unit      string  `json:"unit"`
    CreatedAt string  `json:"created_at"`
}

func main() {
    fmt.Println("[ANALYTICS] Starting consumer...")

    rdb := redis.NewClient(&redis.Options{
        Addr: "redis:6379",
    })

    conn, err := amqp091.Dial("amqp://fitsync:fitsync@rabbitmq:5672/")
    if err != nil {
        log.Fatalf("RabbitMQ connection error: %v", err)
    }
    defer conn.Close()

    ch, err := conn.Channel()
    if err != nil {
        log.Fatalf("Channel error: %v", err)
    }

    ch.ExchangeDeclare("events", "topic", true, false, false, false, nil)

    q, _ := ch.QueueDeclare("analytics.measurements", true, false, false, false, nil)
    ch.QueueBind(q.Name, "measurement.created", "events", false, nil)

    msgs, _ := ch.Consume(q.Name, "", true, false, false, false, nil)

    fmt.Println("[ANALYTICS] Listening for measurement.created ...")

    ctx := context.Background()

    for msg := range msgs {
        fmt.Println("[EVENT] ", string(msg.Body))

        var m MeasurementEvent
        json.Unmarshal(msg.Body, &m)

        redisKey := fmt.Sprintf("stats:user:%d:type:%s", m.UserId, m.Type)

        rdb.IncrByFloat(ctx, redisKey, m.Value)
    }
}
