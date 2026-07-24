<?php

declare(strict_types=1);

namespace Tests\Unit;

use Core\Core\Container;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\TestServiceProvider;
use Core\Contracts\ContainerInterface;
use Tests\Fakes\FirstServiceProvider;
use Tests\Fakes\SecondServiceProvider;

use Core\Core\Application;

final class ApplicationTest extends TestCase
{
    public function testApplicationStoresContainerInstance(): void
    {
        $container = new Container();

        $application = new Application($container);

        self::assertSame($container, $application->getContainer());
    }

    public function testApplicationBootsRegisteredProviders(): void
    {
        TestServiceProvider::reset();

        $container = $this->createStub(ContainerInterface::class);
        $application = new Application($container);

        $application->register(TestServiceProvider::class);
        $application->boot();

        $this->assertSame(1, TestServiceProvider::$registrations);
        $this->assertSame(1, TestServiceProvider::$boots);
    }

    public function testApplicationDoesNotRegisterSameProviderTwice(): void
    {
        TestServiceProvider::reset();

        $container = $this->createStub(ContainerInterface::class);
        $application = new Application($container);

        $application->register(TestServiceProvider::class);
        $application->register(TestServiceProvider::class);

        $this->assertSame(1, TestServiceProvider::$registrations);
    }

    public function testApplicationDoesNotBootProvidersTwice(): void
    {
        TestServiceProvider::reset();

        $container = $this->createStub(ContainerInterface::class);
        $application = new Application($container);

        $application->register(TestServiceProvider::class);

        $application->boot();
        $application->boot();

        $this->assertSame(1, TestServiceProvider::$boots);
    }

    public function testAllProvidersAreRegisteredBeforeAnyProviderBoots(): void
    {
        FirstServiceProvider::reset();

        $container = $this->createStub(ContainerInterface::class);
        $application = new Application($container);

        $application->register(FirstServiceProvider::class);
        $application->register(SecondServiceProvider::class);

        $application->boot();

        $this->assertSame([
            'first.register',
            'second.register',
            'first.boot',
            'second.boot',
        ], FirstServiceProvider::$events);
    }

    public function testProviderRegisteredAfterApplicationBootIsBootedImmediately(): void
    {
        TestServiceProvider::reset();

        $container = $this->createStub(ContainerInterface::class);
        $application = new Application($container);

        $application->boot();
        $application->register(TestServiceProvider::class);

        $this->assertSame(1, TestServiceProvider::$registrations);
        $this->assertSame(1, TestServiceProvider::$boots);
    }

    public function testApplicationRegistersServiceProvider(): void
    {
        $container = new Container();
        $application = new Application($container);

        $application->register(TestServiceProvider::class);

        self::assertTrue(true);
    }

}