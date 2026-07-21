<?php

declare(strict_types=1);

namespace Tests\Fakes;

use Core\Core\ServiceProvider;

final class FirstServiceProvider extends ServiceProvider
{
    /**
     * @var list<string>
     */
    public static array $events = [];

    public function register(): void
    {
        self::$events[] = 'first.register';
    }

    public function boot(): void
    {
        self::$events[] = 'first.boot';
    }

    public static function reset(): void
    {
        self::$events = [];
    }
}