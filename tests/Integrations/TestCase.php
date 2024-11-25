<?php

namespace Orchestra\Workbench\Tests\Integrations;

use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\Workbench\Workbench;
use Orchestra\Workbench\WorkbenchServiceProvider;

#[WithConfig('app.key', 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF')]
#[WithConfig('database.default', 'testing')]
#[WithMigration]
abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use WithWorkbench;

    /** {@inheritDoc} */
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    /** {@inheritDoc} */
    #[\Override]
    protected function defineEnvironment($app)
    {
        Workbench::start($app, static::cachedConfigurationForWorkbench());
    }

    /** {@inheritDoc} */
    protected function getPackageProviders($app)
    {
        return array_merge(parent::getPackageProviders($app), [
            WorkbenchServiceProvider::class,
        ]);
    }
}
