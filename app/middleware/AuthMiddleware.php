<?php

require_once '../app/helpers/JWT.php';
require_once '../app/models/User.php';

class AuthMiddleware
{
    public static function handle(&$request)
    {
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Missing token']);
            exit;
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $payload = JWT::validate($token);

        if (!$payload) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid or expired token']);
            exit;
        }

        $user = User::findById($payload['user_id']);

        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'User not found']);
            exit;
        }

        // 🔒 TOKEN VERSION CHECK (THIS FIXES YOUR LOOPHOLE)
        if ($user['token_version'] !== $payload['token_version']) {
            http_response_code(401);
            echo json_encode(['error' => 'Token revoked']);
            exit;
        }

        $request['user'] = $user;
    }
}
