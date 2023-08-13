<?php

namespace Orchestra\Workbench;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use function Orchestra\Testbench\workbench;

/**
 * @phpstan-import-type TWorkbenchConfig from \Orchestra\Testbench\Foundation\Config
 */
class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('workbench.recipe', function (Application $app) {
            /** @var TWorkbenchConfig $workbench */
            $workbench = workbench();

            return new RecipeManager($app, $workbench);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\BuildCommand::class,
            ]);
        }
    }
}
