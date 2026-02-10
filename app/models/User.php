<?php

require_once '../app/core/Database.php';

class User
{
    public static function findByEmail($email)
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findById($id)
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data)
    {
        $db = Database::connect();
        $stmt = $db->prepare(
            "INSERT INTO users (name, email, password, token_version)
             VALUES (?, ?, ?, 0)"
        );

        $stmt->execute([
            $data['name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
    }

    // 🔥 THIS INVALIDATES OLD TOKENS
    public static function incrementTokenVersion($userId)
    {
        $db = Database::connect();
        $stmt = $db->prepare(
            "UPDATE users SET token_version = token_version + 1 WHERE id = ?"
        );
        $stmt->execute([$userId]);
    }

    public static function storeRefreshToken($userId, $token, $expiry)
    {
        $db = Database::connect();
        $stmt = $db->prepare(
            "UPDATE users SET refresh_token = ?, refresh_expiry = ? WHERE id = ?"
        );
        $stmt->execute([$token, $expiry, $userId]);
    }
}
