<?php

namespace Orchestra\Workbench\Tests;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Orchestra\Testbench\TestCase;
use Orchestra\Workbench\Workbench;
use Orchestra\Workbench\WorkbenchServiceProvider;

class WorkbenchTest extends TestCase
{
    use WithWorkbench;

    /** @test */
    public function it_can_resolve_laravel_path()
    {
        $this->assertSame(
            base_path(), Workbench::laravelPath()
        );

        $this->assertSame(
            base_path('artisan'), Workbench::laravelPath('artisan')
        );
    }


    /** @test */
    public function it_can_resolve_package_path()
    {
        $this->assertSame(
            realpath(__DIR__.'/..'), rtrim(Workbench::packagePath(), DIRECTORY_SEPARATOR)
        );

        $this->assertSame(
            realpath(__DIR__.'/../testbench.yaml'), Workbench::packagePath('testbench.yaml')
        );
    }

    /** @test */
    public function it_can_resolve_workbench_config()
    {
        $config = app(ConfigContract::class)->getWorkbenchAttributes();

        $this->assertSame(
            $config, Workbench::config()
        );

        $this->assertSame(
            $config['start'], Workbench::config('start')
        );
    }
}
