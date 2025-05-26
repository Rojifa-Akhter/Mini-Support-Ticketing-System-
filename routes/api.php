<?php
// routes/api.php

require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/DepartmentController.php';
require_once __DIR__ . '/../controllers/TicketController.php';
require_once __DIR__ . '/../helpers/AuthMiddleware.php';

header('Content-Type: application/json');

$uri = str_replace('/support-system/public/index.php', '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));


error_log("Parsed URI: $uri"); // for debug

$method = $_SERVER['REQUEST_METHOD'];

$authController = new AuthController();

if ($uri === '/api/register' && $method === 'POST') {
    $authController->register();
    exit;
}

if ($uri === '/api/login' && $method === 'POST') {
    $authController->login();
    exit;
}

if ($uri === '/api/logout' && $method === 'POST') {
    $middleware = new AuthMiddleware();
    $middleware->handle();
    $authController->logout();
    exit;
}

// All routes below require auth
$middleware = new AuthMiddleware();
try {
    $middleware->handle();
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

$user = $middleware->user;

$departmentController = new DepartmentController($user);
$ticketController = new TicketController($user);

// Departments routes
if ($uri === '/api/departments') {
    if ($method === 'GET') {
        $departmentController->index();
        exit;
    }
    if ($method === 'POST') {
        $departmentController->create();
        exit;
    }
} elseif (preg_match('#^/api/departments/(\d+)$#', $uri, $matches)) {
    $id = $matches[1];
    if ($method === 'PUT') {
        $departmentController->update($id);
        exit;
    }
    if ($method === 'DELETE') {
        $departmentController->delete($id);
        exit;
    }
}

// Tickets routes
if ($uri === '/api/tickets' && $method === 'POST') {
    $ticketController->create();
    exit;
}

if (preg_match('#^/api/tickets/(\d+)/notes$#', $uri, $matches) && $method === 'POST') {
    $ticket_id = $matches[1];
    $ticketController->addNote($ticket_id);
    exit;
}

if (preg_match('#^/api/tickets/(\d+)/status$#', $uri, $matches) && $method === 'PUT') {
    $ticket_id = $matches[1];
    $ticketController->updateStatus($ticket_id);
    exit;
}

http_response_code(404);
echo json_encode(['error' => 'Endpoint not found']);
exit;
