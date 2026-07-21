<?php

namespace Core\Console\Commands;

use Core\Console\Command;
use Core\Database\Connection;
use Core\Database\Migrator;

class MigrateCommand extends Command
{
    public function name(): string
    {
        return 'migrate';
    }

    public function description(): string
    {
        return 'Executa as migrations pendentes';
    }

    public function handle(array $arguments): void
    {
        $pdo = Connection::connect();

        $migrationsPath = getcwd()
            . DIRECTORY_SEPARATOR
            . 'database'
            . DIRECTORY_SEPARATOR
            . 'migrations';

        $migrator = new Migrator($pdo, $migrationsPath);

        $migrator->migrate();
    }
}