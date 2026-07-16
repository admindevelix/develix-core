<?php

namespace Core\Http;

class Request
{
    public static function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public static function uri(): string
    {
        return parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    }

    public static function input(?string $key = null, mixed $default = null): mixed
    {
        $data = array_merge($_GET, $_POST);

        if ($key === null) {
            return $data;
        }

        return $data[$key] ?? $default;
    }

    public static function all(): array
    {
        return self::input();
    }

    public static function has(string $key): bool
    {
        return array_key_exists($key, self::input());
    }
}