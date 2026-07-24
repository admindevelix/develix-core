<?php

declare(strict_types=1);

namespace Tests\View;

use Core\View\Component;
use PHPUnit\Framework\TestCase;

final class ComponentTest extends TestCase
{
    private string $basePath;

    protected function setUp(): void
    {
        $this->basePath = dirname(__DIR__, 2);

        Component::setBasePath($this->basePath);
    }

    public function testRenderThrowsExceptionWhenComponentDoesNotExist(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Componente não encontrado: inexistente'
        );

        Component::render('inexistente');
    }

    public function testRenderComponentWithData(): void
    {
        $componentDirectory = $this->basePath . '/app/Components';

        if (!is_dir($componentDirectory)) {
            mkdir($componentDirectory, 0777, true);
        }

        $componentPath = $componentDirectory . '/alert.php';

        file_put_contents(
            $componentPath,
            '<div><?php echo $message; ?></div>'
        );

        ob_start();

        try {
            Component::render(
                'alert',
                ['message' => 'Operação realizada']
            );

            $content = ob_get_contents();
        } finally {
            ob_end_clean();

            if (file_exists($componentPath)) {
                unlink($componentPath);
            }
        }

        self::assertSame(
            '<div>Operação realizada</div>',
            $content
        );
    }
}