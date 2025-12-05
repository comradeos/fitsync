<?php

echo "[DEVICES] Device simulator started...\n";

$gatewayUrl = "http://gateway:8080/measurements";

// типи вимірювань
$types = [
    ["type" => "steps", "unit" => "steps"],
    ["type" => "weight", "unit" => "kg"],
    ["type" => "heart_rate", "unit" => "bpm"],
    ["type" => "calories", "unit" => "kcal"],
];

while (true) {

    // рандом
    $t = $types[array_rand($types)];

    $data = [
        "user_id" => 1,
        "device_id" => 1,
        "type" => $t["type"],
        "value" => rand(50, 150),
        "unit" => $t["unit"]
    ];

    echo "[DEVICES] Sending measurement: " . json_encode($data) . "\n";

    // відправка в gateway
    $ch = curl_init($gatewayUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    echo "[DEVICES] Gateway response ($code): $response\n";

    sleep(5);
}
