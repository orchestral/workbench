<?php

namespace Orchestra\Workbench\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Orchestra\Workbench\Workbench;

use function Orchestra\Testbench\after_resolving;

/**
 * @internal
 *
 * @phpstan-import-type TWorkbenchDiscoversConfig from \Orchestra\Testbench\Foundation\Config
 */
final class DiscoverRoutes
{
    /**
     * Bootstrap the given application.
     */
    public function bootstrap(Application $app): void
    {
        /** @var TWorkbenchDiscoversConfig $config */
        $config = Workbench::config('discovers') ?? [
            'web' => false,
            'api' => false,
            'commands' => false,
            'views' => false,
        ];

        tap($app->make('router'), static function (Router $router) use ($config) {
            foreach (['web', 'api'] as $group) {
                if (($config[$group] ?? false) === true) {
                    if (file_exists($route = Workbench::path("routes/{$group}.php"))) {
                        $router->middleware($group)->group($route);
                    }
                }
            }
        });

        if ($app->runningInConsole() && ($config['commands'] ?? false) === true) {
            if (file_exists($console = Workbench::path('routes/console.php'))) {
                require $console;
            }
        }

        after_resolving($app, 'view', static function ($view, $app) use ($config) {
            $path = Workbench::path('/resources/views');

            if (($config['views'] ?? false) === true) {
                $view->addLocation($path);
            }

            $view->addNamespace('workbench', $path);
        });
    }
}
