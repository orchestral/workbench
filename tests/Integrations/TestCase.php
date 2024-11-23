<?php

namespace Orchestra\Workbench\Tests\Integrations;

use Illuminate\Contracts\Config\Repository;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\Foundation\TestbenchServiceProvider;
use Orchestra\Testbench\Workbench\Workbench;
use Orchestra\Workbench\WorkbenchServiceProvider;
use Workbench\App\Models\User;

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

        tap($app->make('config'), static function (Repository $config) {
            $config->set('auth.providers.users.model', User::class);
        });
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
