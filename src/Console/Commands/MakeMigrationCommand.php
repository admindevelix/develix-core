<?php

namespace Core\Console\Commands;

use Core\Console\Command;

class MakeMigrationCommand extends Command
{
    public function name(): string
    {
        return 'make:migration';
    }

    public function description(): string
    {
        return 'Cria uma Migration';
    }

    public function handle(array $arguments): void
    {
        $name = $arguments[2] ?? null;

        if (!$name) {
            echo "Informe o nome da migration.\n";
            echo "Exemplo: php dx make:migration CreateUsersTable\n";
            return;
        }

        $projectPath = getcwd();
        $migrationPath = $projectPath . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations';

        if (!is_dir($migrationPath)) {
            mkdir($migrationPath, 0777, true);
        }

        $timestamp = date('Y_m_d_His');
        $fileName = $timestamp . '_' . $this->toSnakeCase($name) . '.php';
        $fullPath = $migrationPath . DIRECTORY_SEPARATOR . $fileName;

        $content = <<<PHP
<?php

return new class
{
    public function up(): void
    {
        //
    }

    public function down(): void
    {
        //
    }
};

PHP;

        file_put_contents($fullPath, $content);

        echo "Migration criada: database/migrations/{$fileName}\n";
    }

    private function toSnakeCase(string $value): string
    {
        $value = preg_replace('/(?<!^)[A-Z]/', '_$0', $value);

        return strtolower($value);
    }
}