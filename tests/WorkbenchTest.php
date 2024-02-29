<?php

namespace Orchestra\Workbench\Tests;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Orchestra\Testbench\TestCase;
use Orchestra\Workbench\Workbench;
use PHPUnit\Framework\Attributes\Test;

use function Illuminate\Filesystem\join_paths;

class WorkbenchTest extends TestCase
{
    use WithWorkbench;

    #[Test]
    public function it_can_resolve_with_workbench_traits()
    {
        $this->assertTrue(value(app('orchestra.workbench.loaded')));
    }

    #[Test]
    public function it_can_resolve_laravel_path()
    {
        $this->assertSame(
            base_path(), Workbench::laravelPath()
        );

        $this->assertSame(
            base_path('artisan'), Workbench::laravelPath('artisan')
        );
    }

    #[Test]
    public function it_can_resolve_package_path()
    {
        $this->assertSame(
            realpath(__DIR__.'/..'), rtrim(Workbench::packagePath(), DIRECTORY_SEPARATOR)
        );

        $this->assertSame(
            realpath(__DIR__.'/../testbench.yaml'), Workbench::packagePath('testbench.yaml')
        );
    }

    #[Test]
    public function it_can_resolve_workbench_path()
    {
        $this->assertSame(
            realpath(__DIR__.'/../workbench'), rtrim(Workbench::path(), DIRECTORY_SEPARATOR)
        );

        $this->assertSame(
            realpath(__DIR__.'/../workbench/dist/app.js'), Workbench::path(join_paths('dist', 'app.js'))
        );
    }

    #[Test]
    public function it_can_resolve_workbench_config()
    {
        $config = app(ConfigContract::class)->getWorkbenchAttributes();

        $this->assertSame(
            $config, Workbench::config()
        );

        $this->assertSame(
            $config['start'], Workbench::config('start')
        );

        $this->assertSame([
            'config' => false,
            'web' => true,
            'api' => true,
            'commands' => true,
            'components' => false,
            'views' => false,
        ], Workbench::config('discovers'));
    }
}
