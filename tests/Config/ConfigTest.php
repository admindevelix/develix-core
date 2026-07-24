<?php

declare(strict_types=1);

namespace Tests\Config;

use Core\Config\Config;
use Core\Container\Container;
use Core\Core\Application;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    public function testGetReturnsConfigurationValueByKey(): void
    {
        $config = new Config([
            'app_name' => 'Develix',
        ]);

        self::assertSame('Develix', $config->get('app_name'));
    }

    public function testGetReturnsNullWhenKeyDoesNotExist(): void
    {
        $config = new Config();

        self::assertNull($config->get('missing'));
    }

    public function testGetReturnsDefaultValueWhenKeyDoesNotExist(): void
    {
        $config = new Config();

        self::assertSame(
            'Develix',
            $config->get('app_name', 'Develix'),
        );
    }

    public function testGetReturnsNestedConfigurationValue(): void
    {
        $config = new Config([
            'app' => [
                'name' => 'Develix',
            ],
        ]);

        self::assertSame(
            'Develix',
            $config->get('app.name'),
        );
    }

}