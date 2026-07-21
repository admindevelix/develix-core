<?php

declare(strict_types=1);

namespace Core\Core;

use Closure;
use Core\Exceptions\ContainerException;

use Core\Contracts\ContainerInterface;

final class Container implements ContainerInterface
{
    /**
     * @var array<string, Closure>
     */
    private array $bindings = [];

    /**
     * @var array<string, bool>
     */
    private array $singletons = [];

    /**
     * @var array<string, object>
     */
    private array $instances = [];

    public function bind(string $id, Closure $resolver): void
    {
        $this->bindings[$id] = $resolver;
        $this->singletons[$id] = false;

        unset($this->instances[$id]);
    }

    public function singleton(string $id, Closure $resolver): void
    {
        $this->bindings[$id] = $resolver;
        $this->singletons[$id] = true;

        unset($this->instances[$id]);
    }

    public function instance(string $id, object $instance): void
    {
        $this->instances[$id] = $instance;

        unset(
            $this->bindings[$id],
            $this->singletons[$id]
        );
    }

    public function get(string $id): object
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (!isset($this->bindings[$id])) {
            throw new ContainerException(
                "Dependency [{$id}] is not registered in the container."
            );
        }

        $instance = ($this->bindings[$id])($this);

        if (!is_object($instance)) {
            throw new ContainerException(
                "Dependency [{$id}] resolver must return an object."
            );
        }

        if ($this->singletons[$id]) {
            $this->instances[$id] = $instance;
        }

        return $instance;
    }

    public function has(string $id): bool
    {
        return isset($this->bindings[$id])
            || isset($this->instances[$id]);
    }
}