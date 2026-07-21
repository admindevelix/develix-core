<?php

namespace Core\Console;

abstract class Command
{
    abstract public function name(): string;

    abstract public function description(): string;

    abstract public function handle(array $arguments): void;
}