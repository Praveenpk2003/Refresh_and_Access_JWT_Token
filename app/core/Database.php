<?php

class Database
{
    private static $instance;

    public static function connect()
    {
        if (!self::$instance) {
            $dsn = "mysql:host=" . env('DB_HOST') .
                   ";port=" . env('DB_PORT') .
                   ";dbname=" . env('DB_NAME');

            self::$instance = new PDO(
                $dsn,
                env('DB_USER'),
                env('DB_PASS'),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        }

        return self::$instance;
    }
}
