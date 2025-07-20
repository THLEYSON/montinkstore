<?php

namespace App\Models;

use PDO;

class Order
{
    private PDO $pdo;
    private string $table = 'orders';

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(array $data): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} (status, total, email) VALUES (:status, :total, :email)");
        return $stmt->execute([
            ':status' => $data['status'],
            ':total'  => $data['total'],
            ':email'  => $data['email']
        ]);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        return $order ?: null;
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET status = :status WHERE id = :id");
        return $stmt->execute([
            ':status' => $status,
            ':id'     => $id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
