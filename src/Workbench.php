<?php

namespace Orchestra\Workbench;

use Illuminate\Support\Arr;

use function Orchestra\Testbench\join_paths;
use function Orchestra\Testbench\package_path;
use function Orchestra\Testbench\workbench;
use function Orchestra\Testbench\workbench_path;

/**
 * @phpstan-import-type TWorkbenchConfig from \Orchestra\Testbench\Foundation\Config
 *
 * @phpstan-type TStubFiles array{
 *   config: ?string,
 *   'config.basic': ?string,
 *   gitignore: ?string,
 *   'routes.api': ?string,
 *   'routes.console': ?string,
 *   'routes.web': ?string,
 *   'seeders.database': ?string
 * }
 */
class Workbench
{
    /**
     * Files of stub files overrides.
     *
     * @var array<string, ?string>
     *
     * @phpstan-var TStubFiles
     */
    protected static array $files = [
        'config' => null,
        'config.basic' => null,
        'gitignore' => null,
        'routes.api' => null,
        'routes.console' => null,
        'routes.web' => null,
        'seeders.database' => null,
    ];

    /**
     * Get the path to the laravel folder.
     */
    public static function laravelPath(array|string $path = ''): string
    {
        return app()->basePath(
            join_paths(...Arr::wrap(\func_num_args() > 1 ? \func_get_args() : $path))
        );
    }

    /**
     * Get the path to the package folder.
     */
    public static function packagePath(array|string $path = ''): string
    {
        return package_path(
            ...Arr::wrap(\func_num_args() > 1 ? \func_get_args() : $path)
        );
    }

    /**
     * Get the path to the workbench folder.
     */
    public static function path(array|string $path = ''): string
    {
        return workbench_path(
            ...Arr::wrap(\func_num_args() > 1 ? \func_get_args() : $path)
        );
    }

    /**
     * Get the availale configuration.
     *
     * @return array<string, mixed>|mixed
     *
     * @phpstan-return ($key is null ? TWorkbenchConfig : mixed)
     */
    public static function config(?string $key = null): mixed
    {
        return ! \is_null($key)
            ? Arr::get(workbench(), $key)
            : workbench();
    }

    /**
     * Swap stub file by name.
     */
    public static function swapFile(string $name, ?string $file): void
    {
        if (\array_key_exists($name, static::$files)) {
            static::$files[$name] = $file;
        }
    }

    /**
     * Retrieve the stub file from name.
     */
    public static function stubFile(string $name): ?string
    {
        $defaultStub = join_paths(__DIR__, 'Console', 'stubs');

        return transform(
            Arr::get(array_merge([
                'config' => join_paths($defaultStub, 'testbench.yaml'),
                'config.basic' => join_paths($defaultStub, 'testbench.plain.yaml'),
                'gitignore' => join_paths($defaultStub, 'workbench.gitignore'),
                'routes.api' => join_paths($defaultStub, 'routes', 'api.php'),
                'routes.console' => join_paths($defaultStub, 'routes', 'console.php'),
                'routes.web' => join_paths($defaultStub, 'routes', 'web.php'),
                'seeders.database' => join_paths($defaultStub, 'database', 'seeders', 'DatabaseSeeder.php'),
            ], array_filter(static::$files)), $name),
            function ($file) {
                $realpath = realpath($file);

                return $realpath !== false ? $realpath : null;
            }
        );
    }
}
