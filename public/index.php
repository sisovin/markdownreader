<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/constants.php';
require_once base_path('app/Core/Autoloader.php');

use App\Controllers\BookController;
use App\Controllers\AdminController;
use App\Controllers\AdminAssetController;
use App\Controllers\AdminDocumentController;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Core\Autoloader;
use App\Core\Router;
use App\Core\Session;

Autoloader::register(base_path('app'));
Session::start();

$router = new Router();
$router->get(ROUTE_HOME, [HomeController::class, 'index']);
$router->get(ROUTE_LOGIN, [AuthController::class, 'showLogin']);
$router->post(ROUTE_LOGIN, [AuthController::class, 'login']);
$router->get(ROUTE_SIGNUP, [AuthController::class, 'showSignup']);
$router->post(ROUTE_SIGNUP, [AuthController::class, 'signup']);
$router->get(ROUTE_BOOKS . '/{document}', [BookController::class, 'show']);
$router->get(ROUTE_ADMIN_DOCUMENTS . '/create', [AdminDocumentController::class, 'create']);
$router->post(ROUTE_ADMIN_DOCUMENTS . '/preview', [AdminDocumentController::class, 'preview']);
$router->post(ROUTE_ADMIN_DOCUMENTS, [AdminDocumentController::class, 'store']);
$router->get(ROUTE_ADMIN_DOCUMENTS . '/{document}/edit', [AdminDocumentController::class, 'edit']);
$router->post(ROUTE_ADMIN_DOCUMENTS . '/{document}', [AdminDocumentController::class, 'update']);
$router->post(ROUTE_ADMIN_DOCUMENTS . '/{document}/delete', [AdminDocumentController::class, 'destroy']);
$router->post(ROUTE_ADMIN_ASSETS, [AdminAssetController::class, 'store']);
$router->post(ROUTE_ADMIN_ASSETS . '/{asset}/delete', [AdminAssetController::class, 'destroy']);
$router->post(ROUTE_LOGOUT, [AuthController::class, 'logout']);
$router->get(ROUTE_DASHBOARD, [AdminController::class, 'index']);

try {
  $router->dispatch(request_path(), $_SERVER['REQUEST_METHOD'] ?? 'GET');
} catch (Throwable $throwable) {
  http_response_code(500);

  $message = APP_DEBUG
    ? $throwable->getMessage()
    : 'Something went wrong while loading the application.';

  echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Application Error</title><style>body{margin:0;font-family:Segoe UI,sans-serif;background:#08111f;color:#f8fafc;display:grid;place-items:center;min-height:100vh;padding:24px}.card{max-width:720px;border:1px solid rgba(148,163,184,.25);background:rgba(15,23,42,.84);border-radius:24px;padding:32px;box-shadow:0 24px 80px rgba(2,6,23,.4)}h1{margin:0 0 12px;font-size:2rem}p{line-height:1.7;color:#cbd5e1}</style></head><body><div class="card"><h1>Application Error</h1><p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p></div></body></html>';
}
