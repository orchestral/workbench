<?php

namespace Orchestra\Workbench;

use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Orchestra\Testbench\Foundation\Config;
use function Orchestra\Testbench\package_path;

/**
 * @phpstan-import-type TWorkbenchConfig from \Orchestra\Testbench\Foundation\Config
 */
class Workbench
{
    /**
     * Get the path to the laravel folder.
     *
     * @param  string  $path
     * @return string
     */
    public static function laravelPath(string $path = ''): string
    {
        return app()->basePath($path);
    }

    /**
     * Get the path to the package folder.
     *
     * @param  string  $path
     * @return string
     */
    public static function packagePath(string $path = ''): string
    {
        return package_path($path);
    }

    /**
     * Get the path to the workbench folder.
     *
     * @param  string  $path
     * @return string
     */
    public static function path(string $path = ''): string
    {
        return static::packagePath(
            'workbench'.($path != '' ? DIRECTORY_SEPARATOR.ltrim($path, DIRECTORY_SEPARATOR) : '')
        );
    }

    /**
     * Get the availale configuration.
     *
     * @param  string|null  $key
     * @return mixed|array<string, mixed>
     *
     * @phpstan-return ($key is null ? TWorkbenchConfig : mixed)
     */
    public static function config($key = null)
    {
        /** @var \Orchestra\Testbench\Contracts\Config $config */
        $config = app()->bound(ConfigContract::class)
            ? app()->make(ConfigContract::class)
            : new Config();

        $workbench = $config->getWorkbenchAttributes();

        if (! \is_null($key)) {
            return $workbench[$key] ?? null;
        }

        return $workbench;
    }
}
