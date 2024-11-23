<?php

namespace Orchestra\Workbench\Tests\Integrations;

use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\Foundation\TestbenchServiceProvider;
use Orchestra\Testbench\Workbench\Workbench;
use Orchestra\Workbench\WorkbenchServiceProvider;
use Workbench\App\Models\User;

#[WithConfig('auth.providers.users.model', User::class)]
#[WithMigration]
abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use WithWorkbench;

    /** {@inheritDoc} */
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
            TestbenchServiceProvider::class,
            WorkbenchServiceProvider::class,
        ]);
    }
}
