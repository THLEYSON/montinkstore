<?php

use App\Controllers\ProductController;
use App\Controllers\CartController;
use App\Controllers\StockController;
use App\Controllers\HomeController;
use App\Controllers\CouponController;
use App\Controllers\WebhookController;

$router->get('/', [HomeController::class, 'index']);

//Product Routes
$router->group('/product', function ($router) {
    $router->get('', [ProductController::class, 'form']);                 
    $router->post('/store', [ProductController::class, 'store']);        
    $router->post('/update', [ProductController::class, 'update']);      
});

//Product Stock Routes
$router->group('/stock', function ($router) {
    $router->get('/', [StockController::class, 'viewEditStock']); 
    $router->get('/view-stock-details', [StockController::class, 'viewStockDetails']); 
    $router->post('/edit-stock-details', [StockController::class, 'updateStockDetails']);       
    $router->post('/delete-product', [StockController::class, 'deleteProductAndVariation']); 
});


//Home Routes
$router->group('/cart', function ($router) {
    $router->get('', [CartController::class, 'view']);
    $router->post('/add', [CartController::class, 'add']);
    $router->post('/remove', [CartController::class, 'remove']);
    $router->post('/apply-coupon', [CartController::class, 'applyCoupon']);
    $router->get('/summary', [CartController::class, 'summary']);
    $router->post('/checkout', [CartController::class, 'checkout']);
});

//coupon Routes
$router->group('/coupon', function ($router) {
    $router->get('', [CouponController::class, 'index']);
    $router->get('/create', [CouponController::class, 'create']);
    $router->post('/edit', [CouponController::class, 'edit']);
    $router->post('/store', [CouponController::class, 'store']);
    $router->post('/delete', [CouponController::class, 'delete']);
});


$router->post('/webhook/order-status', [WebhookController::class, 'updateOrderStatus']);
