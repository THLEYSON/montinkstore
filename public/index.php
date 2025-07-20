<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Support/Helpers.php';

use Src\Route\Router;
use App\Middleware\AuthTokenMiddleware;

$router = new Router();

$webhookConfig = require __DIR__ . '/../config/webhook.php';
$webhookToken = $webhookConfig['token'] ?? 'SEU_TOKEN_PADRAO';

// $authMiddleware = new AuthTokenMiddleware($webhookToken);

require_once __DIR__ . '/../routes/web.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$router->dispatch($uri, $method);
