<?php

namespace Orchestra\Workbench\Tests\Actions;

use Orchestra\Workbench\WorkbenchServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /** {@inheritDoc} */
    #[\Override]
    protected function getPackageProviders($app)
    {
        return [
            WorkbenchServiceProvider::class,
        ];
    }
}
