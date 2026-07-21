<?php

namespace Core\Console\Commands;

use Core\Console\Command;

class HelpCommand extends Command
{
    public function __construct(
        private array $commands = []
    ) {
    }

    public function setCommands(array $commands): void
    {
        $this->commands = $commands;
    }

    public function name(): string
    {
        return 'help';
    }

    public function description(): string
    {
        return 'Exibe os comandos disponíveis.';
    }

    public function handle(array $arguments): void
    {
        echo PHP_EOL;
        echo "Develix CLI v1.0" . PHP_EOL;
        echo PHP_EOL;
        echo "Comandos disponíveis:" . PHP_EOL;

        foreach ($this->commands as $command) {
            printf(
                "  %-20s %s%s",
                $command->name(),
                $command->description(),
                PHP_EOL
            );
        }

        echo PHP_EOL;
    }
}