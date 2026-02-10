<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$envFile = dirname(__DIR__) . '/.env';

if (!file_exists($envFile)) {
    die('.env file not found at project root');
}

$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($lines as $line) {
    $line = trim($line);

    if ($line === '' || $line[0] === '#') {
        continue;
    }

    [$key, $value] = explode('=', $line, 2);
    $_ENV[trim($key)] = trim($value);
}




require_once '../config/config.php';
require_once '../app/core/Router.php';
require_once '../app/middleware/JsonMiddleware.php';
require_once '../app/middleware/AuthMiddleware.php';
require_once '../app/controllers/AuthController.php';
require_once '../app/controllers/PatientController.php';


$request = JsonMiddleware::handle();

$router = new Router($request);


$router->post('/api/register', [AuthController::class, 'register']);
// $router->post('/register', [AuthController::class, 'register']); 
$router->post('/api/login', [AuthController::class, 'login']);
// $router->post('/login', [AuthController::class, 'login']); 


$router->get('/api/patients', [PatientController::class, 'index'], true);
$router->post('/api/patients', [PatientController::class, 'store'], true);
$router->put('/api/patients/{id}', [PatientController::class, 'update'], true);
$router->delete('/api/patients/{id}', [PatientController::class, 'destroy'], true);
$router->post('/api/refresh', [AuthController::class, 'refresh']);


$router->dispatch();
