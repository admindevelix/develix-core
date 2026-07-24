<?php

declare(strict_types=1);

namespace Tests\View;

use Core\View\View;
use PHPUnit\Framework\TestCase;

final class ViewTest extends TestCase
{
    private string $basePath;

    protected function setUp(): void
    {
        $this->basePath = dirname(__DIR__, 2);

        View::setBasePath($this->basePath);
    }

    public function testRenderThrowsExceptionWhenViewDoesNotExist(): void
    {
        $this->expectException(\RuntimeException::class);

        View::render('view-inexistente');
    }

    public function testRenderViewWithoutLayout(): void
    {
        $viewDirectory = $this->basePath . '/app/Views';

        if (!is_dir($viewDirectory)) {
            mkdir($viewDirectory, 0777, true);
        }

        $viewPath = $viewDirectory . '/test.php';

        file_put_contents(
            $viewPath,
            '<?php echo "View renderizada";'
        );

        ob_start();

        try {
            View::render('test', [], null);

            $content = ob_get_contents();
        } finally {
            ob_end_clean();

            if (file_exists($viewPath)) {
                unlink($viewPath);
            }
        }

        self::assertSame('View renderizada', $content);
    }

    public function testRenderMakesDataAvailableToView(): void
    {
        $viewDirectory = $this->basePath . '/app/Views';

        if (!is_dir($viewDirectory)) {
            mkdir($viewDirectory, 0777, true);
        }

        $viewPath = $viewDirectory . '/user.php';

        file_put_contents(
            $viewPath,
            '<?php echo $name;'
        );

        ob_start();

        try {
            View::render('user', ['name' => 'Diego'], null);

            $content = ob_get_contents();
        } finally {
            ob_end_clean();

            if (file_exists($viewPath)) {
                unlink($viewPath);
            }
        }

        self::assertSame('Diego', $content);
    }

    public function testRenderViewWithLayout(): void
    {
        $viewDirectory = $this->basePath . '/app/Views';
        $layoutDirectory = $this->basePath . '/app/Layouts';

        if (!is_dir($viewDirectory)) {
            mkdir($viewDirectory, 0777, true);
        }

        if (!is_dir($layoutDirectory)) {
            mkdir($layoutDirectory, 0777, true);
        }

        $viewPath = $viewDirectory . '/dashboard.php';
        $layoutPath = $layoutDirectory . '/app.php';

        file_put_contents(
            $viewPath,
            '<?php echo "Conteúdo da view";'
        );

        file_put_contents(
            $layoutPath,
            '<header>Layout</header><?php require $viewPath; ?>'
        );

        ob_start();

        try {
            View::render('dashboard');

            $content = ob_get_contents();
        } finally {
            ob_end_clean();

            if (file_exists($viewPath)) {
                unlink($viewPath);
            }

            if (file_exists($layoutPath)) {
                unlink($layoutPath);
            }
        }

        self::assertSame(
            '<header>Layout</header>Conteúdo da view',
            $content
        );
    }

    public function testRenderThrowsExceptionWhenLayoutDoesNotExist(): void
    {
        $viewDirectory = $this->basePath . '/app/Views';

        if (!is_dir($viewDirectory)) {
            mkdir($viewDirectory, 0777, true);
        }

        $viewPath = $viewDirectory . '/without-layout.php';

        file_put_contents(
            $viewPath,
            '<?php echo "View";'
        );

        try {
            $this->expectException(\RuntimeException::class);
            $this->expectExceptionMessage(
                'Layout não encontrado: inexistente'
            );

            View::render('without-layout', [], 'inexistente');
        } finally {
            if (file_exists($viewPath)) {
                unlink($viewPath);
            }
        }
    }

}