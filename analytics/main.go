package main

import (
    "fmt"
    "time"
)

func main() {
    fmt.Println("Analytics service started (hello from Go)")

    for {
        select {
        case <-time.After(5 * time.Second):
            fmt.Println("Analytics heartbeat...")
        }
    }
}
