<?php

namespace Orchestra\Workbench;

use Illuminate\Support\Arr;

use function Orchestra\Testbench\join_paths;
use function Orchestra\Testbench\package_path;
use function Orchestra\Testbench\workbench;
use function Orchestra\Testbench\workbench_path;

/**
 * @phpstan-import-type TWorkbenchConfig from \Orchestra\Testbench\Foundation\Config
 */
class Workbench extends \Orchestra\Testbench\Workbench\Workbench
{
    /**
     * The Stub Registrar instance.
     */
    protected static ?StubRegistrar $stubRegistrar = null;

    /**
     * Get the path to the application (Laravel) folder.
     */
    public static function applicationPath(array|string $path = ''): string
    {
        return app()->basePath(
            join_paths(...Arr::wrap(\func_num_args() > 1 ? \func_get_args() : $path))
        );
    }

    /**
     * Get the path to the laravel folder.
     */
    public static function laravelPath(array|string $path = ''): string
    {
        return static::applicationPath($path);
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
     * Retrieve Stub Registrar instance.
     */
    public static function stub(): StubRegistrar
    {
        return static::$stubRegistrar ??= new StubRegistrar;
    }

    /**
     * Swap stub file by name.
     */
    public static function swapFile(string $name, ?string $file): void
    {
        static::stub()->swap($name, $file);
    }

    /**
     * Retrieve the stub file from name.
     */
    public static function stubFile(string $name): ?string
    {
        return static::stub()->file($name);
    }
}
