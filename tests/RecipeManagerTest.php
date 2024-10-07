<?php 

namespace Orchestra\Workbench\Tests;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;
use Orchestra\Workbench\RecipeManager;
use Orchestra\Workbench\Contracts\RecipeManager as RecipeManagerContract;
use Orchestra\Workbench\WorkbenchServiceProvider;

class RecipeManagerTest extends TestCase
{
    use WithWorkbench;

    /** @{inheritDoc} */
    #[\Override]
    protected function getPackageProviders($app)
    {
        return array_merge(parent::getPackageProviders($app), [
            WorkbenchServiceProvider::class,
        ]);
    }

    public function test_it_can_be_resolved()
    {
        tap($this->app->make(RecipeManagerContract::class), function ($manager) {
            $this->assertInstanceOf(RecipeManager::class, $manager);
            $this->assertInstanceOf(RecipeManagerContract::class, $manager);
        });
    }
}