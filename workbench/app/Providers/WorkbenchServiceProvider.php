<?php

namespace Workbench\App\Providers;

use Illuminate\Support\ServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->instance('orchestra.workbench.loaded', fn () => true);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
