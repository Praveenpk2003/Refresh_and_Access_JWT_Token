<?php

require_once '../app/models/User.php';
require_once '../app/helpers/JWT.php';
require_once '../app/helpers/Response.php';

class AuthController
{
    public function register()
    {
        $data = $GLOBALS['request']['body'] ?? [];

        if (!isset($data['email'], $data['password'], $data['name'])) {
            Response::error('Missing fields', 422);
        }

        if (User::findByEmail($data['email'])) {
            Response::error('Email already exists', 400);
        }

        User::create($data);
        Response::success('User registered', null, 201);
    }

    public function login()
    {
        $data = $GLOBALS['request']['body'];
        $user = User::findByEmail($data['email']);

        if (!$user || !password_verify($data['password'], $user['password'])) {
            Response::error('Invalid credentials', 401);
        }

        // 🔥 invalidate old access tokens
        User::incrementTokenVersion($user['id']);

        // reload updated token_version
        $user = User::findById($user['id']);

        $payload = [
            'user_id'       => $user['id'],
            'email'         => $user['email'],
            'token_version' => $user['token_version'],
            'iat'           => time(),
            'exp'           => time() + env('JWT_EXPIRY')
        ];

        $accessToken = JWT::generate($payload);

        // refresh token
        $refreshToken  = bin2hex(random_bytes(64));
        $hashedRefresh = password_hash($refreshToken, PASSWORD_DEFAULT);
        $refreshExpiry = time() + env('REFRESH_EXPIRY');

        User::storeRefreshToken($user['id'], $hashedRefresh, $refreshExpiry);

        echo json_encode([
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken
        ]);
        exit;
    }
}
