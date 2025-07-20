<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Stock;
use App\Support\Flash;
use App\Support\View;
use App\Support\Logger;

class ProductController
{
    public function __construct(
        private readonly Product $product,
        private readonly Stock $stock
    ) {}

    public function form(): void
    {
        View::render('products/form', [], 'Register Product');
    }

    public function store(): void
    {
        $name = $_POST['name'] ?? '';
        $variations = $_POST['variations'] ?? [];
        $quantities = $_POST['quantities'] ?? [];
        $variationPrices = $_POST['variation_prices'] ?? [];

        if ($this->isInvalidInput($name, $variations, $quantities, $variationPrices)) {
            Flash::error('Fill in all required fields correctly.');
            $this->redirect('/product');
        }

        try {
            $productId = $this->product->create(trim($name));

            foreach ($variations as $index => $variation) {
                $quantity = (int) $quantities[$index];
                $price = (float) $variationPrices[$index];

                $this->stock->create($productId, trim($variation), $quantity, $price);
            }

            Flash::success('Product saved successfully!');
        } catch (\Throwable $e) {
            Logger::error($e->getMessage());
            Flash::error('Failed to save the product. Try again.');
        }

        $this->redirect('/product');
    }

    private function isInvalidInput(string $name, array $variations, array $quantities, array $prices): bool
    {
        return empty(trim($name)) ||
            empty($variations) || empty($quantities) || empty($prices) ||
            count($variations) !== count($quantities) ||
            count($variations) !== count($prices) ||
            in_array('', $variations, true) ||
            in_array('', $quantities, true) ||
            in_array('', $prices, true);
    }

    private function redirect(string $url): void
    {
        if (!headers_sent()) {
            header("Location: $url");
            exit;
        }
    }
}
