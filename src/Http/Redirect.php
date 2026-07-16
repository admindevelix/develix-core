<?php

namespace Core\Http;

class Redirect
{
    public static function to(string $url): never
    {
        header("Location: {$url}");
        exit;
    }

    public static function back(): never
    {
        $url = $_SERVER['HTTP_REFERER'] ?? '/';

        header("Location: {$url}");
        exit;
    }
}