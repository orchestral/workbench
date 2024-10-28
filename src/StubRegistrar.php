<?php

namespace Orchestra\Workbench;

use Illuminate\Support\Arr;

use function Orchestra\Testbench\join_paths;

/**
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
class StubRegistrar
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
     * Swap stub file by name.
     */
    public function swap(string $name, ?string $file): void
    {
        if (\array_key_exists($name, static::$files)) {
            static::$files[$name] = $file;
        }
    }

    /**
     * Retrieve the stub file from name.
     */
    public static function file(string $name): ?string
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
