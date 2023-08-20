<?php

namespace Orchestra\Workbench\Console;

use Composer\InstalledVersions;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Orchestra\Testbench\Foundation\Console\Concerns\InteractsWithIO;
use Orchestra\Workbench\Composer;
use Orchestra\Workbench\Events\InstallEnded;
use Orchestra\Workbench\Events\InstallStarted;
use Orchestra\Workbench\Workbench;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'workbench:install', description: 'Setup Workbench for package development')]
class InstallCommand extends Command
{
    use InteractsWithIO;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workbench:install {--force : Overwrite any existing files}';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Filesystem $filesystem)
    {
        /** @phpstan-ignore-next-line */
        $workingPath = TESTBENCH_WORKING_PATH;

        event(new InstallStarted($this->input, $this->output, $this->components));

        $this->prepareWorkbenchDirectories($filesystem, $workingPath);
        $this->prepareWorkbenchNamespaces($filesystem, $workingPath);

        $this->copyTestbenchConfigurationFile($filesystem, $workingPath);
        $this->copyTestbenchDotEnvFile($filesystem, $workingPath);

        $this->call('workbench:create-sqlite-db', ['--force' => true]);

        return tap(Command::SUCCESS, function ($exitCode) {
            event(new InstallEnded($this->input, $this->output, $this->components, $exitCode));
        });
    }

    /**
     * Prepare workbench directories.
     */
    protected function prepareWorkbenchDirectories(Filesystem $filesystem, string $workingPath): void
    {
        $workbenchWorkingPath = "{$workingPath}/workbench";

        $this->ensureDirectoryExists($filesystem, "{$workbenchWorkingPath}/app");
        $this->ensureDirectoryExists($filesystem, "{$workbenchWorkingPath}/database/factories");
        $this->ensureDirectoryExists($filesystem, "{$workbenchWorkingPath}/database/migrations");
        $this->ensureDirectoryExists($filesystem, "{$workbenchWorkingPath}/database/seeders");
    }

    /**
     * Prepare workbench namespace to `composer.json`.
     */
    protected function prepareWorkbenchNamespaces(Filesystem $filesystem, string $workingPath): void
    {
        $composer = (new Composer($filesystem))->setWorkingPath($workingPath);

        $composer->modify(function (array $content) use ($filesystem) {
            return $this->appendScriptsToComposer(
                $this->appendAutoloadDevToComposer($content, $filesystem), $filesystem
            );
        });
    }

    /**
     * Append `scripts` to `composer.json`.
     */
    protected function appendScriptsToComposer(array $content, Filesystem $filesystem): array
    {
        $hasScriptsSection = \array_key_exists('scripts', $content);

        if (! $hasScriptsSection) {
            $content['scripts'] = [];
        }

        $postAutoloadDumpScripts = [
            '@clear',
            '@prepare',
        ];

        if (InstalledVersions::isInstalled('orchestra/testbench-dusk')) {
            $postAutoloadDumpScripts[] = '@dusk:install-chromedriver';

            $content['scripts']['dusk:install-chromedriver'] = '@php vendor/bin/dusk-updater detect --auto-update --ansi';
        }

        if (! \array_key_exists('post-autoload-dump', $content['scripts'])) {
            $content['scripts']['post-autoload-dump'] = $postAutoloadDumpScripts;
        } else {
            $content['scripts']['post-autoload-dump'] = array_unique([
                ...$postAutoloadDumpScripts,
                ...Arr::wrap($content['scripts']['post-autoload-dump']),
            ]);
        }

        $content['scripts']['clear'] = '@php vendor/bin/testbench package:purge-skeleton --ansi';
        $content['scripts']['prepare'] = '@php vendor/bin/testbench package:discover --ansi';
        $content['scripts']['build'] = '@php vendor/bin/testbench workbench:build --ansi';
        $content['scripts']['serve'] = [
            '@build',
            '@php vendor/bin/testbench serve',
        ];

        if (! \array_key_exists('lint', $content['scripts'])) {
            $lintScripts = [];

            if (InstalledVersions::isInstalled('laravel/pint')) {
                $lintScripts[] = '@php vendor/bin/pint';
            } elseif ($filesystem->exists(Workbench::packagePath('pint.json'))) {
                $lintScripts[] = 'pint';
            }

            if (InstalledVersions::isInstalled('phpstan/phpstan')) {
                $lintScripts[] = '@php vendor/bin/phpstan analyse';
            }

            if (\count($lintScripts) > 0) {
                $content['scripts']['lint'] = [
                    '@prepare',
                    ...$lintScripts,
                ];
            }
        }

        if (
            $filesystem->exists(Workbench::packagePath('phpunit.xml'))
            || $filesystem->exists(Workbench::packagePath('phpunit.xml.dist'))
        ) {
            if (! \array_key_exists('test', $content['scripts'])) {
                $content['scripts']['test'] = InstalledVersions::isInstalled('pestphp/pest')
                    ? '@php vendor/bin/pest'
                    : '@php vendor/bin/phpunit';
            }
        }

        return $content;
    }

    /**
     * Append `autoload-dev` to `composer.json`.
     */
    protected function appendAutoloadDevToComposer(array $content, Filesystem $filesystem): array
    {
        /** @var array{autoload-dev?: array{psr-4?: array<string, string>}} $content */
        if (! \array_key_exists('autoload-dev', $content)) {
            $content['autoload-dev'] = [];
        }

        /** @var array{autoload-dev: array{psr-4?: array<string, string>}} $content */
        if (! \array_key_exists('psr-4', $content['autoload-dev'])) {
            $content['autoload-dev']['psr-4'] = [];
        }

        $namespaces = [
            'Workbench\\App\\' => 'workbench/app/',
            'Workbench\\Database\\Factories\\' => 'workbench/database/factories/',
            'Workbench\\Database\\Seeders\\' => 'workbench/database/seeders/',
        ];

        foreach ($namespaces as $namespace => $path) {
            if (! \array_key_exists($namespace, $content['autoload-dev']['psr-4'])) {
                $content['autoload-dev']['psr-4'][$namespace] = $path;

                $this->components->task(sprintf(
                    'Added [%s] for [%s] to Composer', $namespace, $path
                ));
            } else {
                $this->components->twoColumnDetail(
                    sprintf('Composer already contain [%s] namespace', $namespace),
                    '<fg=yellow;options=bold>SKIPPED</>'
                );
            }
        }

        return $content;
    }

    /**
     * Copy the "testbench.yaml" file.
     */
    protected function copyTestbenchConfigurationFile(Filesystem $filesystem, string $workingPath): void
    {
        $from = (string) realpath(__DIR__.'/stubs/testbench.yaml');
        $to = "{$workingPath}/testbench.yaml";

        if ($this->option('force') || ! $filesystem->exists($to)) {
            $filesystem->copy($from, $to);

            $this->copyTaskCompleted($from, $to, 'file');
        } else {
            $this->components->twoColumnDetail(
                sprintf('File [%s] already exists', str_replace($workingPath.'/', '', $to)),
                '<fg=yellow;options=bold>SKIPPED</>'
            );
        }
    }

    /**
     * Copy the ".env" file.
     */
    protected function copyTestbenchDotEnvFile(Filesystem $filesystem, string $workingPath): void
    {
        $workbenchWorkingPath = "{$workingPath}/workbench";

        $from = $this->laravel->basePath('.env.example');

        if (! $filesystem->exists($from)) {
            return;
        }

        $choices = Collection::make($this->environmentFiles())
            ->filter(fn ($file) => ! $filesystem->exists("{$workbenchWorkingPath}/{$file}"))
            ->values()
            ->prepend('Skip exporting .env')
            ->all();

        if (! $this->option('force') && empty($choices)) {
            $this->components->twoColumnDetail(
                'File [.env] already exists', '<fg=yellow;options=bold>SKIPPED</>'
            );

            return;
        }

        $choice = $this->components->choice(
            "Export '.env' file as?",
            $choices,
        );

        if ($choice === 'Skip exporting .env') {
            return;
        }

        $to = "{$workbenchWorkingPath}/{$choice}";

        if ($this->option('force') || ! $filesystem->exists($to)) {
            $filesystem->copy($from, $to);
            $filesystem->copy((string) realpath(__DIR__.'/stubs/workbench.gitignore'), "{$workbenchWorkingPath}/.gitignore");

            $this->copyTaskCompleted($from, $to, 'file');
        } else {
            $this->components->twoColumnDetail(
                sprintf('File [%s] already exists', str_replace($workingPath.'/', '', $to)),
                '<fg=yellow;options=bold>SKIPPED</>'
            );
        }
    }

    /**
     * Ensure a directory exists and add `.gitkeep` file.
     */
    protected function ensureDirectoryExists(Filesystem $filesystem, string $workingPath): void
    {
        /** @phpstan-ignore-next-line */
        $rootWorkingPath = TESTBENCH_WORKING_PATH ?? $workingPath;

        if ($filesystem->isDirectory($workingPath)) {
            $this->components->twoColumnDetail(
                sprintf('Directory [%s] already exists', str_replace($rootWorkingPath.'/', '', $workingPath)),
                '<fg=yellow;options=bold>SKIPPED</>'
            );

            return;
        }

        $filesystem->ensureDirectoryExists($workingPath, 0755, true);

        $filesystem->copy((string) realpath(__DIR__.'/stubs/.gitkeep'), "{$workingPath}/.gitkeep");

        $this->components->task(sprintf(
            'Prepare [%s] directory',
            str_replace($rootWorkingPath.'/', '', $workingPath),
        ));
    }

    /**
     * Get possible environment files.
     *
     * @return array<int, string>
     */
    protected function environmentFiles(): array
    {
        $environmentFile = \defined('TESTBENCH_DUSK') && TESTBENCH_DUSK === true
            ? '.env.dusk'
            : '.env';

        return [
            $environmentFile,
            "{$environmentFile}.example",
            "{$environmentFile}.dist",
        ];
    }
}
