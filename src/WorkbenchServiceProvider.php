<?php

namespace Orchestra\Workbench;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('workbench.recipe', function (Application $app) {
            return new RecipeManager($app);
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
