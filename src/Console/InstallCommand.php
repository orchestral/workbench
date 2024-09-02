<?php

namespace Orchestra\Workbench\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Orchestra\Testbench\Foundation\Console\Actions\GeneratesFile;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

use function Orchestra\Testbench\package_path;

#[AsCommand(name: 'workbench:install', description: 'Setup Workbench for package development')]
class InstallCommand extends Command
{
    /**
     * The `testbench.yaml` default configuration file.
     */
    public static ?string $configurationBaseFile;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Filesystem $filesystem)
    {
        if (! $this->option('skip-devtool')) {
            $devtool = match (true) {
                \is_bool($this->option('devtool')) => $this->option('devtool'),
                default => $this->components->confirm('Install Workbench DevTool?', true),
            };

            if ($devtool === true) {
                $this->call('workbench:devtool', [
                    '--force' => $this->option('force'),
                    '--no-install' => true,
                    '--basic' => $this->option('basic'),
                ]);
            }
        }

        $workingPath = package_path();

        $this->copyTestbenchConfigurationFile($filesystem, $workingPath);
        $this->copyTestbenchDotEnvFile($filesystem, $workingPath);

        $this->call('workbench:create-sqlite-db', ['--force' => true]);

        return Command::SUCCESS;
    }

    /**
     * Copy the "testbench.yaml" file.
     */
    protected function copyTestbenchConfigurationFile(Filesystem $filesystem, string $workingPath): void
    {
        $from = (string) realpath(static::$configurationBaseFile ?? __DIR__.'/stubs/'.($this->option('basic') === true ? 'testbench.plain.yaml' : 'testbench.yaml'));
        $to = "{$workingPath}/testbench.yaml";

        (new GeneratesFile(
            filesystem: $filesystem,
            components: $this->components,
            force: (bool) $this->option('force'),
        ))->handle($from, $to);
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
            ->reject(static fn ($file) => $filesystem->exists("{$workbenchWorkingPath}/{$file}"))
            ->values()
            ->prepend('Skip exporting .env')
            ->all();

        if (! $this->option('force') && empty($choices)) {
            $this->components->twoColumnDetail(
                'File [.env] already exists', '<fg=yellow;options=bold>SKIPPED</>'
            );

            return;
        }

        $choice = $this->components->choice("Export '.env' file as?", $choices);

        if ($choice === 'Skip exporting .env') {
            return;
        }

        $to = "{$workbenchWorkingPath}/{$choice}";

        (new GeneratesFile(
            filesystem: $filesystem,
            components: $this->components,
            force: (bool) $this->option('force'),
        ))->handle($from, $to);

        (new GeneratesFile(
            filesystem: $filesystem,
            force: (bool) $this->option('force'),
        ))->handle((string) realpath(__DIR__.'/stubs/workbench.gitignore'), "{$workbenchWorkingPath}/.gitignore");
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

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Overwrite any existing files'],
            ['devtool', null, InputOption::VALUE_NEGATABLE, 'Run DevTool installation'],
            ['basic', null, InputOption::VALUE_NONE, 'Skipped routes and discovers installation'],

            /** @deprecated */
            ['skip-devtool', null, InputOption::VALUE_NONE, 'Skipped DevTool installation (deprecated)'],
        ];
    }
}
