const WebSocket = require('ws');

const wss = new WebSocket.Server({ port: 9002 });

console.log("[WS] WebSocket server started on ws://0.0.0.0:9002");

wss.on('connection', (ws) => {
    console.log("[WS] Client connected");

    ws.on('close', () => {
        console.log("[WS] Client disconnected");
    });
});

// Метод для надсилання повідомлення з notifier
function broadcast(msg) {
    wss.clients.forEach(client => {
        if (client.readyState === WebSocket.OPEN) {
            client.send(msg);
        }
    });
}

// Додаємо HTTP endpoint щоб Notifier міг надсилати дані
const http = require('http');

http.createServer((req, res) => {
    if (req.method === "POST") {
        let body = "";
        req.on('data', chunk => body += chunk);
        req.on('end', () => {
            console.log("[WS] Broadcasting:", body);
            broadcast(body);
            res.writeHead(200);
            res.end("OK");
        });
    } else {
        res.writeHead(404);
        res.end();
    }
}).listen(9003);

console.log("[WS] HTTP endpoint listening on http://0.0.0.0:9003");
