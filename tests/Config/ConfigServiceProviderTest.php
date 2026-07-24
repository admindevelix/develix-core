<?php

declare(strict_types=1);

namespace Tests\Config;

use Core\Config\Config;
use Core\Config\ConfigServiceProvider;
use Core\Core\Container;
use PHPUnit\Framework\TestCase;

final class ConfigServiceProviderTest extends TestCase
{
    public function testRegisterAddsConfigAsSingletonToContainer(): void
    {
        $container = new Container();
        $provider = new ConfigServiceProvider($container);

        $provider->register();

        $firstConfig = $container->get(Config::class);
        $secondConfig = $container->get(Config::class);

        self::assertInstanceOf(Config::class, $firstConfig);
        self::assertSame($firstConfig, $secondConfig);
    }

    public function testRegisterProvidesInitialConfigurationValues(): void
    {
        $container = new Container();

        $provider = new ConfigServiceProvider(
            $container,
            [
                'app' => [
                    'name' => 'Develix',
                ],
            ],
        );

        $provider->register();

        $config = $container->get(Config::class);

        self::assertSame('Develix', $config->get('app.name'));
    }
}