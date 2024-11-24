<?php

namespace Orchestra\Workbench;

use Composer\InstalledVersions;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Orchestra\Canvas\Core\PresetManager;
use Orchestra\Testbench\Foundation\Events\ServeCommandEnded;
use Orchestra\Testbench\Foundation\Events\ServeCommandStarted;

use function Orchestra\Testbench\join_paths;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Contracts\RecipeManager::class, static fn (Application $app) => new RecipeManager($app));

        $this->callAfterResolving(PresetManager::class, static function ($manager) {
            $manager->extend('workbench', static fn (Application $app) => new GeneratorPreset($app));

            $manager->setDefaultDriver('workbench');
        });

        AboutCommand::add('Workbench', static fn () => array_filter([
            'Version' => InstalledVersions::getPrettyVersion('orchestra/workbench'),
        ]));
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $hasAuthentication = Workbench::config('auth') === true;

        Collection::make(['workbench'])
            ->when($hasAuthentication, static fn ($routes) => $routes->push('workbench-auth'))
            ->mapWithKeys(static fn ($route) => [$route => (string) realpath(join_paths(__DIR__, '..', 'routes', "{$route}.php"))])
            ->filter(static fn ($route) => is_file($route))
            ->each(function ($route) {
                $this->loadRoutesFrom($route);
            });

        $this->app->make(HttpKernel::class)->pushMiddleware(Http\Middleware\CatchDefaultRoute::class);

        if ($hasAuthentication) {
            $this->loadViewsFrom((string) realpath(join_paths(__DIR__, '..', 'resources', 'views')), '');

            $this->loadViewComponentsAs('', [
                View\Components\AppLayout::class,
                View\Components\GuestLayout::class,
            ]);

            $this->loadAnonymousComponentsFrom((string) realpath(join_paths(__DIR__, '..', 'resources', 'views', 'components')));
        }

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

            if ($hasAuthentication) {
                $this->publishes([
                    __DIR__.'/../public/' => public_path(''),
                ], ['laravel-assets']);
            }
        }
    }

    /**
     * Register a view file namespace.
     *
     * @param  string|array  $path
     * @param  string  $namespace
     * @return void
     */
    #[\Override]
    protected function loadViewsFrom($path, $namespace)
    {
        if (empty($namespace)) {
            $this->callAfterResolving('view', static function ($view) use ($path) {
                $view->getFinder()->addLocation($path);
            });
        }

        parent::loadViewsFrom($path, $namespace);
    }

    /**
     * Register the given view components with a custom prefix.
     */
    protected function loadAnonymousComponentsFrom(string $path, ?string $prefix = null): void
    {
        $this->callAfterResolving(BladeCompiler::class, static function ($blade) use ($path, $prefix) {
            $blade->anonymousComponentPath($path, $prefix);
        });
    }
}
