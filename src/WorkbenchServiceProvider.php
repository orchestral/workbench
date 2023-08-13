<?php

namespace Orchestra\Workbench;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
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
        static::authenticationRoutes();

        $this->app->make(HttpKernel::class)->pushMiddleware(Http\Middleware\CatchDefaultRoute::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\BuildCommand::class,
            ]);
        }
    }

    /**
     * Provide the authentication routes for Testbench.
     *
     * @return void
     */
    public static function authenticationRoutes()
    {
        Route::group(array_filter([
            'prefix' => '_workbench',
            'middleware' => 'web',
        ]), function (Router $router) {
            $router->get(
                '/', [Http\Controllers\WorkbenchController::class, 'start']
            )->name('workbench.start');

            $router->get(
                '/login/{userId}/{guard?}', [Http\Controllers\WorkbenchController::class, 'login']
            )->name('workbench.login');

            $router->get(
                '/logout/{guard?}', [Http\Controllers\WorkbenchController::class, 'logout']
            )->name('workbench.logout');

            $router->get(
                '/user/{guard?}', [Http\Controllers\WorkbenchController::class, 'user']
            )->name('workbench.user');
        });
    }
}
