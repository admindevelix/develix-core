<?php

declare(strict_types=1);

namespace Tests\Unit;

use Core\Core\Container;
use Core\Exceptions\ContainerException;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ContainerTest extends TestCase
{
    public function testBindAndResolveDependency(): void
    {
        $container = new Container();

        $container->bind(
            'service',
            static fn (): stdClass => new stdClass()
        );

        $service = $container->get('service');

        self::assertInstanceOf(stdClass::class, $service);
    }

    public function testSingletonReturnsSameInstance(): void
    {
        $container = new Container();

        $container->singleton(
            'service',
            static fn (): stdClass => new stdClass()
        );

        $first = $container->get('service');
        $second = $container->get('service');

        self::assertSame($first, $second);
    }

    public function testInstanceCanBeRegisteredDirectly(): void
    {
        $container = new Container();
        $service = new stdClass();

        $container->instance('service', $service);

        self::assertSame($service, $container->get('service'));
    }

    public function testHasReturnsTrueForRegisteredDependency(): void
    {
        $container = new Container();

        $container->bind(
            'service',
            static fn (): stdClass => new stdClass()
        );

        self::assertTrue($container->has('service'));
    }

    public function testHasReturnsFalseForUnknownDependency(): void
    {
        $container = new Container();

        self::assertFalse($container->has('unknown'));
    }

    public function testUnknownDependencyThrowsContainerException(): void
    {
        $container = new Container();

        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage(
            'Dependency [unknown] is not registered in the container.'
        );

        $container->get('unknown');
    }

    public function testBindingReceivesContainerInstance(): void
    {
        $container = new Container();

        $container->instance('config', new stdClass());

        $container->bind(
            'service',
            static fn (Container $container): object => $container->get('config')
        );

        self::assertSame(
            $container->get('config'),
            $container->get('service')
        );
    }

    public function testResolverMustReturnObject(): void
    {
        $container = new Container();

        $container->bind(
            'invalid',
            static fn (): string => 'invalid'
        );

        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage(
            'Dependency [invalid] resolver must return an object.'
        );

        $container->get('invalid');
    }
}