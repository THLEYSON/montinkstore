<?php

namespace App\Models;

use PDO;
use PDOException;

class Coupon
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM coupons ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM coupons WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $coupon = $stmt->fetch(PDO::FETCH_ASSOC);
        return $coupon ?: null;
    }

    public function findByCode(string $code): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM coupons WHERE code = :code AND expires_at >= CURRENT_DATE");
        $stmt->execute(['code' => $code]);
        $coupon = $stmt->fetch(PDO::FETCH_ASSOC);
        return $coupon ?: null;
    }

    public function create(array $data): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO coupons (code, discount, expires_at, min_subtotal)
            VALUES (:code, :discount, :expires_at, :min_subtotal)
        ");
        return $stmt->execute([
            'code'         => $data['code'],
            'discount'     => $data['discount'],
            'expires_at'   => $data['expires_at'],
            'min_subtotal' => $data['min_subtotal'] ?? 0
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE coupons SET
                code = :code,
                discount = :discount,
                expires_at = :expires_at,
                min_subtotal = :min_subtotal
            WHERE id = :id
        ");
        return $stmt->execute([
            'id'           => $id,
            'code'         => $data['code'],
            'discount'     => $data['discount'],
            'expires_at'   => $data['expires_at'],
            'min_subtotal' => $data['min_subtotal'] ?? 0
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM coupons WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}
