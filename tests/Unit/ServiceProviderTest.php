<?php

declare(strict_types=1);

namespace Tests\Unit;

use Core\Contracts\ContainerInterface;
use Core\Core\Container;
use Core\Core\ServiceProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ServiceProviderTest extends TestCase
{
    public function testProviderCanRegisterDependencies(): void
    {
        $container = new Container();

        $provider = new class ($container) extends ServiceProvider {
            public function register(): void
            {
                $this->container->instance('service', new stdClass());
            }
        };

        $provider->register();

        self::assertTrue($container->has('service'));
        self::assertInstanceOf(stdClass::class, $container->get('service'));
    }

    public function testBootMethodIsOptional(): void
    {
        $container = new Container();

        $provider = new class ($container) extends ServiceProvider {
            public function register(): void
            {
            }
        };

        $provider->boot();

        self::assertTrue(true);
    }

    public function testProviderStoresContainerInstance(): void
    {
        $container = new Container();

        $provider = new class ($container) extends ServiceProvider {
            public function register(): void
            {
            }

            public function getContainer(): ContainerInterface
            {
                return $this->container;
            }
        };

        self::assertSame($container, $provider->getContainer());
    }
}