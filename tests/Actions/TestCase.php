<?php

namespace Orchestra\Workbench\Tests\Actions;

use Orchestra\Canvas\Core\LaravelServiceProvider as CanvasCoreServiceProvider;
use Orchestra\Workbench\WorkbenchServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /** {@inheritDoc} */
    #[\Override]
    protected function getPackageProviders($app)
    {
        return [
            CanvasCoreServiceProvider::class,
            WorkbenchServiceProvider::class,
        ];
    }
}
