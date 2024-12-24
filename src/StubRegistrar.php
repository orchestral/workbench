<?php

namespace Orchestra\Workbench;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;

use function Orchestra\Testbench\join_paths;

/**
 * @internal
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

    /**
     * Replace stub namespaces.
     */
    public static function replaceInFile(Filesystem $filesystem, string $filename): void
    {
        if (! $filesystem->isFile($filename)) {
            return;
        }

        $workbenchAppNamespacePrefix = rtrim(Workbench::detectNamespace('app') ?? 'Workbench\App\\', '\\');
        $workbenchFactoriesNamespacePrefix = rtrim(Workbench::detectNamespace('database/factories') ?? 'Workbench\Database\Factories\\', '\\');
        $workbenchSeederNamespacePrefix = rtrim(Workbench::detectNamespace('database/seeders') ?? 'Workbench\Database\Seeders\\', '\\');

        $serviceProvider = \sprintf('%s\Providers\WorkbenchServiceProvider', $workbenchAppNamespacePrefix);
        $databaseSeeder = \sprintf('%s\DatabaseSeeder', $workbenchSeederNamespacePrefix);
        $userModel = \sprintf('%s\Models\User', $workbenchAppNamespacePrefix);
        $userFactory = \sprintf('%s\UserFactory', $workbenchFactoriesNamespacePrefix);

        $keywords = [
            'Workbench\App' => $workbenchAppNamespacePrefix,
            'Workbench\Database\Factories' => $workbenchFactoriesNamespacePrefix,
            'Workbench\Database\Seeders' => $workbenchSeederNamespacePrefix,

            '{{WorkbenchAppNamespace}}' => $workbenchAppNamespacePrefix,
            '{{ WorkbenchAppNamespace }}' => $workbenchAppNamespacePrefix,
            '{{WorkbenchFactoryNamespace}}' => $workbenchFactoriesNamespacePrefix,
            '{{ WorkbenchFactoryNamespace }}' => $workbenchFactoriesNamespacePrefix,
            '{{WorkbenchSeederNamespace}}' => $workbenchSeederNamespacePrefix,
            '{{ WorkbenchSeederNamespace }}' => $workbenchSeederNamespacePrefix,

            '{{WorkbenchServiceProvider}}' => $serviceProvider,
            '{{ WorkbenchServiceProvider }}' => $serviceProvider,

            '{{WorkbenchDatabaseSeeder}}' => $databaseSeeder,
            '{{ WorkbenchDatabaseSeeder }}' => $databaseSeeder,

            '{{WorkbenchUserModel}}' => $userModel,
            '{{ WorkbenchUserModel }}' => $userModel,

            '{{WorkbenchUserFactory}}' => $userFactory,
            '{{ WorkbenchUserFactory }}' => $userFactory,
            'Orchestra\Testbench\Factories\UserFactory' => $userFactory,
        ];

        $filesystem->put($filename, str_replace(array_keys($keywords), array_values($keywords), $filesystem->get($filename)));
    }
}
