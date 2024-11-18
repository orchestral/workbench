<?php

namespace Orchestra\Workbench\Tests;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Orchestra\Testbench\TestCase;
use Orchestra\Workbench\Workbench;
use PHPUnit\Framework\Attributes\Test;

use function Orchestra\Testbench\package_path;

class WorkbenchTest extends TestCase
{
    use WithWorkbench;

    #[Test]
    public function it_can_resolve_stub_files()
    {
        $this->assertSame(
            package_path('src', 'Console', 'stubs', 'database', 'seeders', 'DatabaseSeeder.php'),
            Workbench::stubFile('seeders.database')
        );

        $this->assertNull(Workbench::stubFile('testbench'));
    }

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

        $this->assertSame(
            realpath(__FILE__), Workbench::packagePath('tests', 'WorkbenchTest.php')
        );

        $this->assertSame(
            realpath(__FILE__), Workbench::packagePath(['tests', 'WorkbenchTest.php'])
        );
    }

    #[Test]
    public function it_can_resolve_workbench_path()
    {
        $this->assertSame(
            realpath(__DIR__.'/../workbench'), rtrim(Workbench::path(), DIRECTORY_SEPARATOR)
        );

        $this->assertSame(
            realpath(__DIR__.'/../workbench/dist/app.js'), Workbench::path('dist'.DIRECTORY_SEPARATOR.'app.js')
        );

        $this->assertSame(
            realpath(__DIR__.'/../workbench/dist/app.js'), Workbench::path('dist', 'app.js')
        );

        $this->assertSame(
            realpath(__DIR__.'/../workbench/dist/app.js'), Workbench::path(['dist', 'app.js'])
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
            'factories' => false,
            'web' => true,
            'api' => true,
            'commands' => true,
            'components' => false,
            'views' => false,
        ], Workbench::config('discovers'));
    }
}
