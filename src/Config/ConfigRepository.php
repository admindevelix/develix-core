<?php

declare(strict_types=1);

namespace Core\Config;

final class ConfigRepository
{
    /**
     * @param array<string, mixed> $items
     */
    public function create(array $items = []): Config
    {
        return new Config($items);
    }
}