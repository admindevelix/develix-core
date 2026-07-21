<?php

namespace Core\Console;

use Core\Console\Commands\HelpCommand;
use Core\Console\Commands\MakeControllerCommand;
use Core\Console\Commands\MakeMigrationCommand;
use Core\Console\Commands\MigrateCommand;
use Core\Console\Commands\MakeModelCommand;

class Application
{
    /**
     * @var Command[]
     */
    private array $commands = [];

    public function __construct()
    {
        $help = new HelpCommand();

        $this->register($help);
        $this->register(new MakeControllerCommand());
        $this->register(new MakeMigrationCommand());
        $this->register(new MigrateCommand());
        $this->register(new MakeModelCommand());

        $help->setCommands($this->commands);
    }

    public function register(Command $command): void
    {
        $this->commands[$command->name()] = $command;
    }

    public function run(array $arguments): void
    {
        $commandName = $arguments[1] ?? 'help';

        if (!isset($this->commands[$commandName])) {
            echo "Comando '{$commandName}' não encontrado." . PHP_EOL . PHP_EOL;

            $this->commands['help']->handle([]);
            return;
        }

        $this->commands[$commandName]->handle($arguments);
    }

    public function commands(): array
    {
        return $this->commands;
    }
}