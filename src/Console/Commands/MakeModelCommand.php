<?php

namespace Core\Console\Commands;

use Core\Console\Command;

class MakeModelCommand extends Command
{
    public function name(): string
    {
        return 'make:model';
    }

    public function description(): string
    {
        return 'Cria um Model na aplicação.';
    }

    public function handle(array $arguments): void
    {
        $name = $arguments[2] ?? null;

        if (!$name) {
            echo "Informe o nome do model." . PHP_EOL;
            echo "Exemplo: php dx make:model User" . PHP_EOL;
            return;
        }

        $modelName = str_ends_with($name, 'Model')
            ? $name
            : $name . 'Model';

        $projectPath = getcwd();

        $modelsPath = $projectPath
            . DIRECTORY_SEPARATOR
            . 'app'
            . DIRECTORY_SEPARATOR
            . 'Models';

        if (!is_dir($modelsPath)) {
            mkdir($modelsPath, 0777, true);
        }

        $filePath = $modelsPath
            . DIRECTORY_SEPARATOR
            . $modelName
            . '.php';

        if (file_exists($filePath)) {
            echo "Model já existe: app/Models/{$modelName}.php" . PHP_EOL;
            return;
        }

        $content = <<<PHP
<?php

namespace App\Models;

use Core\Core\Model;

class {$modelName} extends Model
{
}

PHP;

        file_put_contents($filePath, $content);

        echo "Model criado: app/Models/{$modelName}.php" . PHP_EOL;
    }
}