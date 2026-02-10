<?php

class Response
{
    public static function json($data, int $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public static function success($message, $data = null, int $status = 200)
    {
        self::json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    public static function error($message, int $status = 400)
    {
        self::json([
            'success' => false,
            'error' => $message
        ], $status);
    }
}
