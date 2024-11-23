<?php

namespace Orchestra\Workbench\Tests\Integrations;

use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\Foundation\TestbenchServiceProvider;
use Orchestra\Testbench\Workbench\Workbench;
use Orchestra\Workbench\WorkbenchServiceProvider;

#[WithMigration]
abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use WithWorkbench;

    protected function defineEnvironment($app)
    {
        $this->withoutVite();
        
        Workbench::start($app, static::cachedConfigurationForWorkbench());
    }

    /** {@inheritDoc} */
    protected function getPackageProviders($app)
    {
        return array_merge(parent::getPackageProviders($app), [
            TestbenchServiceProvider::class,
            WorkbenchServiceProvider::class,
        ]);
    }
}
