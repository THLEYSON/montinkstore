<?php

namespace App\Controllers;

use App\Models\Stock;
use App\Models\Coupon;
use App\Support\Flash;
use App\Support\Logger;
use App\Support\View;
use PDO;

class CartController
{
    private readonly PDO $pdo;
    private readonly Stock $stock;

    public function __construct(PDO $pdo, Stock $stock)
    {
        $this->pdo = $pdo;
        $this->stock = $stock;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function add(): void
    {
        $stockId  = $_POST['stock_id'] ?? null;
        $quantity = $_POST['quantity'] ?? 1;

        if (!is_numeric($stockId) || !is_numeric($quantity) || $quantity <= 0) {
            $this->jsonOrRedirect('error', 'Invalid product or quantity.');
            return;
        }

        try {
            $item = $this->stock->getById((int)$stockId);

            if (!$item || $item['quantity'] < $quantity) {
                $this->jsonOrRedirect('error', 'Insufficient stock.');
                return;
            }

            $cart = &$_SESSION['cart'];
            $cart[$stockId] = [
                'stock_id'     => $stockId,
                'product_name' => $item['product_name'],
                'variation'    => $item['variation'],
                'price'        => $item['price'],
                'quantity'     => ($cart[$stockId]['quantity'] ?? 0) + $quantity
            ];

            $this->jsonOrRedirect('success', 'Added to cart!', ['cart' => $cart]);
        } catch (\Throwable $e) {
            Logger::error($e->getMessage());
            $this->jsonOrRedirect('error', 'Unexpected error.');
        }
    }

    public function remove(): void
    {
        try {
            $stockId = $_POST['stock_id'] ?? null;

            if (!isset($_SESSION['cart'][$stockId])) {
                $this->jsonOrRedirect('error', 'Item not found in cart.', [], '/cart');
                return;
            }

            unset($_SESSION['cart'][$stockId]);

            $this->jsonOrRedirect('success', 'Item removed from cart.', [], '/cart');
        } catch (\Throwable $e) {
            Logger::error($e->getMessage());
            $this->jsonOrRedirect('error', 'Failed to remove item.', [], '/cart');
        }
    }

    public function applyCoupon(): void
    {
        try {
            $code = trim($_POST['code'] ?? '');
            if (!$code) {
                echo json_encode(['status' => 'error', 'message' => 'Coupon code is required.']);
                return;
            }

            $couponModel = new Coupon($this->pdo);
            $coupon = $couponModel->findByCode($code);

            if (!$coupon) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid or expired coupon.']);
                return;
            }

            $_SESSION['coupon'] = $coupon;

            echo json_encode([
                'status' => 'success',
                'message' => 'Coupon applied.',
                'discount' => $coupon['discount'],
                'code' => $coupon['code']
            ]);
        } catch (\Throwable $e) {
            Logger::error($e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Unexpected error applying coupon.']);
        }
    }

    public function summary(): void
    {
        try {
            $cart = $_SESSION['cart'] ?? [];
            $coupon = $_SESSION['coupon'] ?? null;
            $subtotal = $this->calculateSubtotal($cart);

            $discount = 0;
            if ($coupon && $subtotal >= (float)($coupon['min_value'] ?? 0)) {
                $discount = (float) $coupon['discount'];
            }

            $freight = $this->calculateFreight($subtotal);
            $total = max(0, $subtotal + $freight - $discount);

            header('Content-Type: application/json');
            echo json_encode([
                'cart'     => array_values($cart),
                'subtotal' => $subtotal,
                'freight'  => $freight,
                'discount' => $discount,
                'total'    => $total,
                'coupon'   => $coupon['code'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Logger::error($e->getMessage());
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to load cart summary.']);
        }
    }

    public function view(): void
    {
        try {
            $cart = $_SESSION['cart'] ?? [];
            $coupon = $_SESSION['coupon'] ?? null;
            $subtotal = $this->calculateSubtotal($cart);

            $discount = 0;
            if ($coupon && $subtotal >= (float)($coupon['min_value'] ?? 0)) {
                $discount = (float) $coupon['discount'];
            }

            $freight = $this->calculateFreight($subtotal);
            $total = max(0, $subtotal + $freight - $discount);

            View::render('cart/index', compact('cart', 'subtotal', 'freight', 'discount', 'total'), 'Your Cart');
        } catch (\Throwable $e) {
            Logger::error($e->getMessage());
            Flash::error('Unable to display cart.');
            $this->redirect('/');
        }
    }

    public function checkout(): void
    {
        try {
            if (empty($_SESSION['cart'])) {
                Flash::error("Cart is empty.");
                $this->redirect('/');
                return;
            }

            $email = trim($_POST['email'] ?? '');
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Flash::error("Invalid email address.");
                $this->redirect('/cart');
                return;
            }

            $cart = $_SESSION['cart'] ?? [];

            if (!$this->validateStock($cart)) {
                return; 
            }

            $subtotal = $this->calculateSubtotal($cart);
            $freight = $this->calculateFreight($subtotal);
            $total = $subtotal + $freight;

            $this->decrementStock($cart);

            $message = "<h3>Thanks for your purchase!</h3><ul>";
            foreach ($cart as $item) {
                $message .= "<li>" . htmlspecialchars($item['product_name'] ?? '') . " - " . 
                            htmlspecialchars($item['variation'] ?? '') . " (" . 
                            ($item['quantity'] ?? 0) . "x R$" . 
                            number_format($item['price'] ?? 0, 2, ',', '.') . ")</li>";
            }
            $message .= "</ul>";
            $message .= "<p><strong>Subtotal:</strong> R$ " . number_format($subtotal, 2, ',', '.') . "</p>";
            $message .= "<p><strong>Frete:</strong> R$ " . number_format($freight, 2, ',', '.') . "</p>";
            $message .= "<p><strong>Total:</strong> R$ " . number_format($total, 2, ',', '.') . "</p>";

            $mailer = new \App\Support\Mailer();
            $mailer->send($email, "🧾 Purchase Confirmation", $message);

            unset($_SESSION['cart'], $_SESSION['coupon']);
            Flash::success("Purchase completed! Confirmation sent to $email");
            $this->redirect('/');
        } catch (\Throwable $e) {
            Logger::error($e->getMessage());
            Flash::error('Checkout failed.');
            $this->redirect('/');
        }
    }


    private function calculateFreight(float $subtotal): float
    {
        if ($subtotal === 0 || $subtotal > 200) return 0.00;
        if ($subtotal >= 52 && $subtotal <= 166.59) return 15.00;
        return 20.00;
    }

    private function calculateSubtotal(array $cart): float
    {
        return array_reduce($cart, function ($sum, $item) {
            return $sum + ($item['price'] * $item['quantity']);
        }, 0);
    }

    private function jsonOrRedirect(string $status, string $message, array $extra = [], string $redirect = '/'): void
    {
        if ($this->isAjax()) {
            echo json_encode(array_merge([
                'status' => $status,
                'message' => $message
            ], $extra));
            exit;
        }

        $status === 'success' ? Flash::success($message) : Flash::error($message);
        $this->redirect($redirect);
    }

    private function redirect(string $url): void
    {
        if (!headers_sent()) {
            header("Location: $url");
            exit;
        }
    }

    private function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    private function validateStock(array $cart): bool
    {
        $stockModel = new \App\Models\Stock($this->pdo);

        foreach ($cart as $stockId => $item) {
            $requestedQty = $item['quantity'] ?? null;

            if (!ctype_digit((string) $stockId) || !is_numeric($requestedQty) || $requestedQty <= 0) {
                Flash::error("Invalid cart item data.");
                $this->redirect('/cart');
                return false;
            }

            $stockItem = $stockModel->findById((int) $stockId);
            if (!$stockItem) {
                Flash::error("Product not found: " . htmlspecialchars($item['product_name'] ?? ''));
                $this->redirect('/cart');
                return false;
            }

            if ($stockItem['quantity'] < (int) $requestedQty) {
                Flash::error("Insufficient stock for: " . htmlspecialchars($stockItem['variation'] ?? ''));
                $this->redirect('/cart');
                return false;
            }
        }

        return true;
    }

    private function decrementStock(array $cart): void
    {
        $stockModel = new \App\Models\Stock($this->pdo);

        foreach ($cart as $item) {
            $stockId = $item['stock_id'] ?? null;
            $requestedQty = $item['quantity'] ?? 0;

            if ($stockId && is_numeric($requestedQty)) {
                $stockModel->decrementStock((int) $stockId, (int) $requestedQty);
            }
        }

    }

}
