<?php

class JsonMiddleware
{
    public static function handle()
    {
        header('Content-Type: application/json');

        $method = $_SERVER['REQUEST_METHOD'];
        $allowed = ['POST', 'PUT'];

        $request = [];

        if (in_array($method, $allowed)) {
            if (!str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')) {
                http_response_code(415);
                echo json_encode(['error' => 'Content-Type must be application/json']);
                exit;
            }

            $raw = file_get_contents('php://input');
            if (!$raw) {
                http_response_code(400);
                echo json_encode(['error' => 'Empty JSON body']);
                exit;
            }

            $decoded = json_decode($raw, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid JSON']);
                exit;
            }

            $request['body'] = $decoded;
        }

        return $request;
    }
}
