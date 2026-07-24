<?php

declare(strict_types=1);

namespace Tests\Config;

use Core\Config\Config;
use Core\Config\ConfigRepository;
use PHPUnit\Framework\TestCase;

final class ConfigRepositoryTest extends TestCase
{
    public function testCreateReturnsConfigInstance(): void
    {
        $repository = new ConfigRepository();

        $config = $repository->create([]);

        self::assertInstanceOf(
            Config::class,
            $config
        );
    }

    public function testCreateProvidesItemsToConfig(): void
    {
        $repository = new ConfigRepository();

        $config = $repository->create([
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