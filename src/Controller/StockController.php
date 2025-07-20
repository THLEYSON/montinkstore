<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Stock;
use App\Support\Flash;
use App\Support\View;
use App\Support\Logger;

class StockController
{
    public function __construct(
        private readonly Product $product,
        private readonly Stock $stock
    ) {}

    public function viewStockDetails(): void
    {
        $stocks = $this->stock->allWithProduct();
        View::render('/stock/edit-stock-details', compact('stocks'), 'Update Products');
    }

    public function updateStockDetails(): void
    {
        $variations = $_POST['variations'] ?? [];
        $prices     = $_POST['prices'] ?? [];
        $quantities = $_POST['quantities'] ?? [];

        if (empty($variations) || empty($prices) || empty($quantities)) {
            Flash::error('Missing required fields.');
            $this->redirect('/stock/view-stock-details');
        }

        try {
            foreach ($variations as $stockId => $variation) {
                $price    = $prices[$stockId] ?? null;
                $quantity = $quantities[$stockId] ?? null;

                if (
                    !is_string($variation) || trim($variation) === '' ||
                    $price === null || !is_numeric($price) ||
                    $quantity === null || !is_numeric($quantity)
                ) {
                    continue;
                }

                $this->stock->updateVariationAndPrice((int)$stockId, trim($variation), (float)$price);
                $this->stock->updateQuantity((int)$stockId, (int)$quantity);
            }

            Flash::success('Variations, prices, and stock quantities updated successfully!');
        } catch (\Throwable $e) {
            Logger::error($e->getMessage());
            Flash::error('Failed to update. Try again.');
        }

        $this->redirect('/stock/view-stock-details');
    }

    public function deleteProductAndVariation(): void
    {
        $productId = $_POST['product_id'] ?? null;
        $stockId   = $_POST['stock_id'] ?? null;

        if (!$this->isValidNumber($productId) || !$this->isValidNumber($stockId)) {
            Flash::error('Invalid deletion request.');
            $this->redirect('/stock/view-stock-details');
        }

        try {
            $this->stock->deleteById((int)$stockId);

            if ($this->stock->countByProductId((int)$productId) === 0) {
                $this->product->deleteById((int)$productId);
            }

            Flash::success('Product variation deleted successfully.');
        } catch (\Throwable $e) {
            Logger::error($e->getMessage());
            Flash::error('Failed to delete product or variation.');
        }

        $this->redirect('/stock/view-stock-details');
    }

    private function isValidNumber($value): bool
    {
        return is_numeric($value) && trim((string)$value) !== '';
    }

    private function redirect(string $path): void
    {
        if (!headers_sent()) {
            header("Location: {$path}");
            exit;
        }
    }
}
