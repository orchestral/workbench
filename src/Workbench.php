<?php

namespace Orchestra\Workbench;

use Illuminate\Support\Arr;

/**
 * @phpstan-import-type TWorkbenchConfig from \Orchestra\Testbench\Foundation\Config
 */
class Workbench
{
    /**
     * Get the path to the laravel folder.
     */
    public static function laravelPath(array|string $path = ''): string
    {
        return app()->basePath(
            \Orchestra\Testbench\join_paths(...Arr::wrap(\func_num_args() > 1 ? \func_get_args() : $path))
        );
    }

    /**
     * Get the path to the package folder.
     */
    public static function packagePath(array|string $path = ''): string
    {
        return \Orchestra\Testbench\package_path(
            ...Arr::wrap(\func_num_args() > 1 ? \func_get_args() : $path)
        );
    }

    /**
     * Get the path to the workbench folder.
     */
    public static function path(array|string $path = ''): string
    {
        return \Orchestra\Testbench\workbench_path(
            ...Arr::wrap(\func_num_args() > 1 ? \func_get_args() : $path)
        );
    }

    /**
     * Get the availale configuration.
     *
     * @param  string|null  $key
     * @return array<string, mixed>|mixed
     *
     * @phpstan-return ($key is null ? TWorkbenchConfig : mixed)
     */
    public static function config($key = null)
    {
        $workbench = \Orchestra\Testbench\workbench();

        if (! \is_null($key)) {
            return $workbench[$key] ?? null;
        }

        return $workbench;
    }
}
