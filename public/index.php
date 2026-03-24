<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Http\Middleware\TenantResolver;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\WebhookController;

// -----------------------------
// BASIC REQUEST SETUP
// -----------------------------
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// -----------------------------
// TENANT RESOLUTION
// -----------------------------
$tenantResolver = new TenantResolver();
$tenantResolver->handle();

// -----------------------------
// SIMPLE ROUTER
// -----------------------------
try {

    if ($uri === '/' && $method === 'GET') {
        echo json_encode([
            "status" => "ok",
            "message" => "SaaS Billing API Running"
        ]);
        exit;
    }

    // -------------------------
    // SUBSCRIPTION ROUTES
    // -------------------------
    if ($uri === '/subscribe' && $method === 'POST') {
        $controller = new SubscriptionController();
        echo json_encode($controller->create());
        exit;
    }

    if ($uri === '/upgrade' && $method === 'POST') {
        $controller = new SubscriptionController();
        echo json_encode($controller->upgrade());
        exit;
    }

    if ($uri === '/usage' && $method === 'GET') {
        $controller = new SubscriptionController();
        echo json_encode($controller->usage());
        exit;
    }

    // -------------------------
    // WEBHOOK ROUTES
    // -------------------------
    if ($uri === '/webhook' && $method === 'POST') {
        $controller = new WebhookController();
        echo json_encode($controller->handle());
        exit;
    }

    // -------------------------
    // NOT FOUND
    // -------------------------
    http_response_code(404);
    echo json_encode([
        "error" => "Route not found"
    ]);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        "error" => $e->getMessage()
    ]);
}
