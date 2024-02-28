<?php

namespace Orchestra\Workbench;

use Illuminate\Support\Collection;

/**
 * @phpstan-import-type TWorkbenchConfig from \Orchestra\Testbench\Foundation\Config
 */
class Workbench
{
    /**
     * Get the path to the laravel folder.
     */
    public static function laravelPath(string $path = ''): string
    {
        return app()->basePath($path);
    }

    /**
     * Get the path to the package folder.
     */
    public static function packagePath(string $path = ''): string
    {
        return \Orchestra\Testbench\package_path($path);
    }

    /**
     * Get the path to the workbench folder.
     */
    public static function path(string $path = ''): string
    {
        return \Orchestra\Testbench\workbench_path($path);
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
        $workbench = \Orchestra\Testbench\workbench();

        if (! \is_null($key)) {
            return $workbench[$key] ?? null;
        }

        return $workbench;
    }

    /**
     * Get Workbench build steps.
     *
     * @return \Illuminate\Support\Collection<string, array<string, mixed>>
     */
    public static function buildSteps(): Collection
    {
        /** @var array<int|string, array<string, mixed>|string> $build */
        $build = static::config('build');

        return Collection::make($build)
            ->mapWithKeys(static function (array|string $build) {
                /** @var string $name */
                $name = match (true) {
                    \is_array($build) => array_key_first($build),
                    \is_string($build) => $build,
                };

                /** @var array<string, mixed> $options */
                $options = match (true) {
                    \is_array($build) => $build[array_key_first($build)],
                    \is_string($build) => [],
                };

                return [
                    $name => Collection::make($options)->mapWithKeys(static fn ($value, $key) => [$key => $value])->all(),
                ];
            });
    }
}
