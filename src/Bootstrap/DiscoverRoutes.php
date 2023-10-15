<?php

namespace Orchestra\Workbench\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Orchestra\Workbench\Workbench;

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
    }
}
