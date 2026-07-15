<?php

namespace Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    public static function connect(): PDO
    {
        if (self::$connection === null) {

            $host = '127.0.0.1';
            $database = 'develix';
            $user = 'root';
            $password = '';

            try {

                self::$connection = new PDO(
                    "mysql:host={$host};dbname={$database};charset=utf8mb4",
                    $user,
                    $password
                );

                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            } catch (PDOException $e) {

                die($e->getMessage());

            }
        }

        return self::$connection;
    }
}