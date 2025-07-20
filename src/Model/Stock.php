<?php

namespace App\Models;

use PDO;

class Stock
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(int $productId, string $variation, int $quantity, float $price): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO stock (product_id, variation, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$productId, $variation, $quantity, $price]);
    }

    public function allWithProduct(): array
    {
        $sql = "
            SELECT 
                s.id, 
                s.product_id, 
                s.variation, 
                s.quantity, 
                s.price,
                p.name AS product_name
            FROM 
                stock AS s
            INNER JOIN 
                products AS p ON p.id = s.product_id
            ORDER BY 
                p.name ASC, 
                s.variation ASC
        ";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function updateQuantityAndPrice(int $id, int $quantity, float $price): bool
    {
        $stmt = $this->pdo->prepare("UPDATE stock SET quantity = ?, price = ? WHERE id = ?");
        return $stmt->execute([$quantity, $price, $id]);
    }

    public function updateQuantity(int $id, int $quantity): bool
    {
        $stmt = $this->pdo->prepare("UPDATE stock SET quantity = ? WHERE id = ?");
        return $stmt->execute([$quantity, $id]);
    }

    public function updateVariationAndPrice(int $id, string $variation, float $price): bool
    {
        $stmt = $this->pdo->prepare("UPDATE stock SET variation = ?, price = ? WHERE id = ?");
        return $stmt->execute([$variation, $price, $id]);
    }

    public function getProductIdByStockId(int $stockId): ?int
    {
        $stmt = $this->pdo->prepare("SELECT product_id FROM stock WHERE id = ?");
        $stmt->execute([$stockId]);
        $result = $stmt->fetch();

        return $result['product_id'] ?? null;
    }

    public function deleteById(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM stock WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function countByProductId(int $productId): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM stock WHERE product_id = ?");
        $stmt->execute([$productId]);
        return (int) $stmt->fetchColumn();
    }

    public function getById(int $stockId): ?array
    {
        $sql = "
            SELECT 
                s.id,
                s.product_id,
                s.variation,
                s.quantity,
                s.price,
                p.name AS product_name
            FROM 
                stock AS s
            INNER JOIN 
                products AS p ON p.id = s.product_id
            WHERE 
                s.id = ?
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$stockId]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM stock WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function decrementStock(int $id, int $quantity): bool
    {
        $stmt = $this->pdo->prepare("UPDATE stock SET quantity = quantity - ? WHERE id = ? AND quantity >= ?");
        return $stmt->execute([$quantity, $id, $quantity]);
    }

}
