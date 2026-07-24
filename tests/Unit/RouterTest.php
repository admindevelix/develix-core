<?php

declare(strict_types=1);

namespace Tests\Unit;

use Core\Core\Router;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    protected function setUp(): void
    {
        TestController::$executedAction = null;

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/';
    }

    protected function tearDown(): void
    {
        unset(
            $_SERVER['REQUEST_METHOD'],
            $_SERVER['REQUEST_URI']
        );
    }

    public function testDispatchesRegisteredGetRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/produtos';

        $router = new Router();

        $router->get(
            '/produtos',
            [TestController::class, 'index']
        );

        $router->dispatch();

        self::assertSame(
            'index',
            TestController::$executedAction
        );
    }

    public function testDispatchesRegisteredPostRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/produtos';

        $router = new Router();

        $router->post(
            '/produtos',
            [TestController::class, 'store']
        );

        $router->dispatch();

        self::assertSame(
            'store',
            TestController::$executedAction
        );
    }

    public function testNormalizesRouteUri(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/produtos/';

        $router = new Router();

        $router->get(
            'produtos',
            [TestController::class, 'index']
        );

        $router->dispatch();

        self::assertSame(
            'index',
            TestController::$executedAction
        );
    }
}

final class TestController
{
    public static ?string $executedAction = null;

    public function index(): void
    {
        self::$executedAction = 'index';
    }

    public function store(): void
    {
        self::$executedAction = 'store';
    }
}