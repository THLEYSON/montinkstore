<?php

namespace Src\Route;

use App\Middleware\MiddlewareInterface;
use App\Support\Database;

class Router
{
    private array $routes = [];
    private array $middlewares = [];
    private string $currentGroupPrefix = '';
    private array $currentGroupMiddlewares = [];

    public function middleware(string $route, MiddlewareInterface $middleware): void
    {
        if (!is_subclass_of($middleware, MiddlewareInterface::class)) {
            throw new \InvalidArgumentException("Middleware must implement MiddlewareInterface");
        }

        $this->middlewares[$route][] = $middleware;
    }

    public function get(string $path, callable|array $handler)
    {
        $this->registerRoute('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler)
    {
        $this->registerRoute('POST', $path, $handler);
    }

    public function group(string $prefix, callable $callback, array $middlewares = []): void
    {
        $previousPrefix = $this->currentGroupPrefix;
        $previousMiddlewares = $this->currentGroupMiddlewares;

        $this->currentGroupPrefix .= rtrim($prefix, '/');
        $this->currentGroupMiddlewares = array_merge($this->currentGroupMiddlewares, $middlewares);

        $callback($this);

        $this->currentGroupPrefix = $previousPrefix;
        $this->currentGroupMiddlewares = $previousMiddlewares;
    }

    private function registerRoute(string $method, string $path, callable|array $handler): void
    {
        $fullPath = $this->prefixPath($path);
        $this->routes[$method][$fullPath] = $handler;

        if (!empty($this->currentGroupMiddlewares)) {
            foreach ($this->currentGroupMiddlewares as $middleware) {
                $this->middlewares[$fullPath][] = $middleware;
            }
        }
    }

    private function prefixPath(string $path): string
    {
        $full = rtrim($this->currentGroupPrefix . '/' . ltrim($path, '/'), '/');
        return $full === '' ? '/' : $full;
    }

    public function dispatch(string $uri, string $method): void
    {
        $uri = rtrim($uri, '/');
        if ($uri === '') {
            $uri = '/';
        }

        if (isset($this->routes[$method][$uri])) {
            $handler = $this->routes[$method][$uri];
            $middlewares = $this->middlewares[$uri] ?? [];

            $runner = array_reduce(
                array_reverse($middlewares),
                fn($next, MiddlewareInterface $middleware) => fn() => $middleware->handle($next),
                fn() => $this->runHandler($handler, $uri)
            );

            $runner();
        } else {
            http_response_code(404);
            echo '404 Not Found';
        }
    }

    private function runHandler(callable|array $handler, string $uri): void
    {
        if (is_array($handler)) {
            [$class, $method] = $handler;

            $pdo = Database::getConnection();

            switch ($class) {
                case \App\Controllers\StockController::class:
                    $productModel = new \App\Models\Product($pdo);
                    $stockModel = new \App\Models\Stock($pdo);
                    $controller = new $class($productModel, $stockModel);
                    break;

                case \App\Controllers\ProductController::class:
                    $productModel = new \App\Models\Product($pdo);
                    $stockModel = new \App\Models\Stock($pdo);
                    $controller = new $class($productModel, $stockModel);
                    break;

                case \App\Controllers\CartController::class:
                    case \App\Controllers\CartController::class:
                    $stockModel = new \App\Models\Stock($pdo);
                    $controller = new $class($pdo, $stockModel);
                    break;

                case \App\Controllers\CouponController::class:
                    $couponModel = new \App\Models\Coupon($pdo);
                    $controller = new $class($couponModel);
                    break;

                case \App\Controllers\WebhookController::class:
                    $orderModel = new \App\Models\Order($pdo);
                    $controller = new $class($orderModel);
                    break;
                case \App\Controllers\HomeController::class:
                    $productModel = new \App\Models\Product($pdo);
                    $controller = new $class($productModel);
                    break;

                default:
                    $controller = new $class();
                    break;
            }

            $controller->$method();
        } else {
            $handler();
        }
    }
}
