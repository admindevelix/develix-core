<?php

namespace Core\Database;

use PDO;
use RuntimeException;
use Throwable;

class Migrator
{
    public function __construct(
        private PDO $pdo,
        private string $migrationsPath
    ) {
    }

    public function migrate(): void
    {
        if (!is_dir($this->migrationsPath)) {
            throw new RuntimeException(
                "Diretório de migrations não encontrado: {$this->migrationsPath}"
            );
        }

        $this->createMigrationsTable();

        $executedMigrations = $this->getExecutedMigrations();

        $files = glob(
            $this->migrationsPath . DIRECTORY_SEPARATOR . '*.php'
        ) ?: [];

        sort($files);

        foreach ($files as $file) {
            $migrationName = basename($file);

            if (in_array($migrationName, $executedMigrations, true)) {
                continue;
            }

            $migration = require $file;

            if (!is_object($migration) || !method_exists($migration, 'up')) {
                throw new RuntimeException(
                    "Migration inválida: {$migrationName}"
                );
            }

            try {
                $migration->up($this->pdo);

                $statement = $this->pdo->prepare(
                    'INSERT INTO migrations (migration) VALUES (:migration)'
                );

                $statement->execute([
                    'migration' => $migrationName,
                ]);

                echo "Migrada: {$migrationName}" . PHP_EOL;
            } catch (Throwable $exception) {
                throw new RuntimeException(
                    "Erro ao executar {$migrationName}: {$exception->getMessage()}",
                    0,
                    $exception
                );
            }
        }

        echo "Migrations finalizadas." . PHP_EOL;
    }

    private function createMigrationsTable(): void
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    private function getExecutedMigrations(): array
    {
        $statement = $this->pdo->query(
            'SELECT migration FROM migrations ORDER BY id'
        );

        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }
}