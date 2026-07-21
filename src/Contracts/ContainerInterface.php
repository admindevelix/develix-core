<?php

declare(strict_types=1);

namespace Core\Contracts;

use Closure;

interface ContainerInterface
{
    public function bind(string $id, Closure $resolver): void;

    public function singleton(string $id, Closure $resolver): void;

    public function instance(string $id, object $instance): void;

    public function get(string $id): object;

    public function has(string $id): bool;
}