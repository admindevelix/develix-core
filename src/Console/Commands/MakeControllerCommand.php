<?php

namespace Core\Console\Commands;

use Core\Console\Command;

class MakeControllerCommand extends Command
{
    public function name(): string
    {
        return 'make:controller';
    }

    public function description(): string
    {
        return 'Cria um Controller na aplicação.';
    }

    public function handle(array $arguments): void
    {
        $name = $arguments[2] ?? null;

        if (!$name) {
            echo "Informe o nome do Controller." . PHP_EOL;
            echo "Exemplo: php dx make:controller ProdutoController" . PHP_EOL;
            return;
        }

        if (!str_ends_with($name, 'Controller')) {
            $name .= 'Controller';
        }

        $basePath = getcwd();
        $directory = $basePath . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Controllers';
        $filePath = $directory . DIRECTORY_SEPARATOR . $name . '.php';

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (file_exists($filePath)) {
            echo "O Controller {$name} já existe." . PHP_EOL;
            return;
        }

        $content = <<<PHP
<?php

namespace App\Controllers;

use Core\Http\Controller;

class {$name} extends Controller
{
}
PHP;

        file_put_contents($filePath, $content . PHP_EOL);

        echo "Controller criado: app/Controllers/{$name}.php" . PHP_EOL;
    }
}