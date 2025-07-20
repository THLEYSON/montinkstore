<?php

namespace App\Controllers;

use PDO;
use App\Support\Logger;

class WebhookController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function updateOrderStatus(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['id'], $data['status'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing id or status']);
            return;
        }

        $id = (int) $data['id'];
        $status = trim($data['status']);

        try {
            if (strtolower($status) === 'canceled') {
                $stmt = $this->pdo->prepare("DELETE FROM orders WHERE id = :id");
                $stmt->execute(['id' => $id]);
                http_response_code(200);
                echo json_encode(['message' => 'Order deleted']);
            } else {
                $stmt = $this->pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
                $stmt->execute(['id' => $id, 'status' => $status]);
                http_response_code(200);
                echo json_encode(['message' => 'Order status updated']);
            }
        } catch (\Throwable $e) {
            Logger::error("Webhook error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error']);
        }
    }
}
