<?php

namespace App\Controllers;

use App\Models\Product;
use App\Support\View;

class HomeController
{
    public function __construct(
        private readonly Product $product
    ) {}

    public function index(): void
    {
        $products = $this->product->allWithStock();

        View::render('home/index', compact('products'), 'Home');
    }
}
