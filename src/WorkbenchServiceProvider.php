<?php

namespace Orchestra\Workbench;

use Composer\InstalledVersions;
use Illuminate\Console\Generators\PresetManager;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;
use Orchestra\Testbench\Foundation\Events\ServeCommandEnded;
use Orchestra\Testbench\Foundation\Events\ServeCommandStarted;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Contracts\RecipeManager::class, static function (Application $app) {
            return new RecipeManager($app);
        });

        if (class_exists(PresetManager::class)) {
            $this->callAfterResolving(PresetManager::class, function ($manager, $app) {
                $manager->extend('workbench', function ($app) {
                    return new GeneratorPreset($app);
                });

                $manager->setDefaultDriver('workbench');
            });
        }

        AboutCommand::add('Workbench', fn () => array_filter([
            'Version' => InstalledVersions::getPrettyVersion('orchestra/workbench'),
        ]));
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom((string) realpath(__DIR__.'/../routes/workbench.php'));

        $this->app->make(HttpKernel::class)->pushMiddleware(Http\Middleware\CatchDefaultRoute::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\BuildCommand::class,
                Console\CreateSqliteDbCommand::class,
                Console\DropSqliteDbCommand::class,
                Console\InstallCommand::class,
                Console\DevToolCommand::class,
            ]);

            tap($this->app->make('events'), static function (EventDispatcher $event) {
                $event->listen(ServeCommandStarted::class, [Listeners\AddAssetSymlinkFolders::class, 'handle']);
                $event->listen(ServeCommandEnded::class, [Listeners\RemoveAssetSymlinkFolders::class, 'handle']);
            });
        }
    }
}
