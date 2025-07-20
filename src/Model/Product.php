<?php

namespace App\Models;

use PDO;

class Product
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(string $name): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO products (name) VALUES (?)");
        $stmt->execute([$name]);
        return (int) $this->pdo->lastInsertId();
    }

    public function all(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM products ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allWithStock(): array
    {
        $stmt = $this->pdo->query("
            SELECT 
                p.name,
                s.id,
                s.variation,
                s.price,
                s.quantity
            FROM stock s
            JOIN products p ON p.id = s.product_id
            ORDER BY p.name ASC, s.variation ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update(int $id, string $name, float $price): bool
    {
        $stmt = $this->pdo->prepare("UPDATE products SET name = ?, price = ? WHERE id = ?");
        return $stmt->execute([$name, $price, $id]);
    }

    public function updateName(int $id, string $name): bool
    {
        $stmt = $this->pdo->prepare("UPDATE products SET name = ? WHERE id = ?");
        return $stmt->execute([$name, $id]);
    }

    public function deleteById(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

}
