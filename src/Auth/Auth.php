<?php

namespace Core\Auth;

use Core\Session\Session;

class Auth
{
    private const SESSION_KEY = 'auth_user';

    public static function login(array $user): void
    {
        Session::put(self::SESSION_KEY, $user);
    }

    public static function user(): ?array
    {
        return Session::get(self::SESSION_KEY);
    }

    public static function id(): mixed
    {
        return self::user()['id'] ?? null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function guest(): bool
    {
        return !self::check();
    }

    public static function logout(): void
    {
        Session::forget(self::SESSION_KEY);
    }
}