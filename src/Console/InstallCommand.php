<?php

namespace Orchestra\Workbench\Console;

use Composer\InstalledVersions;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Orchestra\Testbench\Foundation\Console\Actions\GeneratesFile;
use Orchestra\Workbench\StubRegistrar;
use Orchestra\Workbench\Workbench;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function Orchestra\Sidekick\join_paths;
use function Orchestra\Testbench\package_path;

#[AsCommand(name: 'workbench:install', description: 'Setup Workbench for package development')]
class InstallCommand extends Command
{
    use Concerns\InteractsWithFiles;

    /**
     * The `testbench.yaml` default configuration file.
     */
    public static ?string $configurationBaseFile = null;

    /**
     * Determine if Package also uses Testbench Dusk.
     */
    protected ?bool $hasTestbenchDusk = null;

    /** {@inheritDoc} */
    #[\Override]
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->hasTestbenchDusk = InstalledVersions::isInstalled('orchestra/testbench-dusk');

        parent::initialize($input, $output);
    }

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

        $this->replaceDefaultLaravelSkeletonInTestbenchConfigurationFile($filesystem, $workingPath);

        $this->call('workbench:create-sqlite-db', ['--force' => true]);

        return Command::SUCCESS;
    }

    /**
     * Copy the "testbench.yaml" file.
     */
    protected function copyTestbenchConfigurationFile(Filesystem $filesystem, string $workingPath): void
    {
        $from = ! \is_null(static::$configurationBaseFile)
            ? (string) realpath(static::$configurationBaseFile)
            : (string) Workbench::stubFile($this->option('basic') === true ? 'config.basic' : 'config');

        $to = join_paths($workingPath, 'testbench.yaml');

        (new GeneratesFile(
            filesystem: $filesystem,
            components: $this->components,
            force: (bool) $this->option('force'),
        ))->handle($from, $to);

        StubRegistrar::replaceInFile($filesystem, $to);
    }

    /**
     * Copy the ".env" file.
     */
    protected function copyTestbenchDotEnvFile(Filesystem $filesystem, string $workingPath): void
    {
        $workbenchWorkingPath = join_paths($workingPath, 'workbench');

        $from = $this->laravel->basePath('.env.example');

        if (! $filesystem->isFile($this->laravel->basePath('.env.example'))) {
            return;
        }

        $choices = Collection::make($this->environmentFiles())
            ->reject(static fn ($file) => $filesystem->isFile(join_paths($workbenchWorkingPath, $file)))
            ->values();

        if (! $this->option('force') && $choices->isEmpty()) {
            $this->components->twoColumnDetail(
                'File [.env] already exists', '<fg=yellow;options=bold>SKIPPED</>'
            );

            return;
        }

        /** @var string|null $targetEnvironmentFile */
        $targetEnvironmentFile = $this->input->isInteractive()
            ? $this->components->choice(
                "Export '.env' file as?",
                $choices->prepend('Skip exporting .env')->all()
            ) : null;

        if (\in_array($targetEnvironmentFile, [null, 'Skip exporting .env'])) {
            return;
        }

        $filesystem->ensureDirectoryExists($workbenchWorkingPath);

        $this->generateSeparateEnvironmentFileForTestbenchDusk($filesystem, $workbenchWorkingPath, $targetEnvironmentFile);

        (new GeneratesFile(
            filesystem: $filesystem,
            components: $this->components,
            force: (bool) $this->option('force'),
        ))->handle(
            $from,
            join_paths($workbenchWorkingPath, $targetEnvironmentFile)
        );

        (new GeneratesFile(
            filesystem: $filesystem,
            force: (bool) $this->option('force'),
        ))->handle(
            (string) Workbench::stubFile('gitignore'),
            join_paths($workbenchWorkingPath, '.gitignore')
        );
    }

    /**
     * Replace the default `laravel` skeleton for Testbench Dusk.
     *
     * @codeCoverageIgnore
     */
    protected function replaceDefaultLaravelSkeletonInTestbenchConfigurationFile(Filesystem $filesystem, string $workingPath): void
    {
        if ($this->hasTestbenchDusk === false) {
            return;
        }

        $this->replaceInFile($filesystem, ["laravel: '@testbench'"], ["laravel: '@testbench-dusk'"], join_paths($workingPath, 'testbench.yaml'));
    }

    /**
     * Generate separate `.env.dusk` equivalent for Testbench Dusk.
     *
     * @codeCoverageIgnore
     */
    protected function generateSeparateEnvironmentFileForTestbenchDusk(Filesystem $filesystem, string $workbenchWorkingPath, string $targetEnvironmentFile): void
    {
        if ($this->hasTestbenchDusk === false) {
            return;
        }

        if ($this->components->confirm('Create separate environment file for Testbench Dusk?', false)) {
            (new GeneratesFile(
                filesystem: $filesystem,
                components: $this->components,
                force: (bool) $this->option('force'),
            ))->handle(
                $this->laravel->basePath('.env.example'),
                join_paths($workbenchWorkingPath, str_replace('.env', '.env.dusk', $targetEnvironmentFile))
            );
        }
    }

    /**
     * Get possible environment files.
     *
     * @return array<int, string>
     */
    protected function environmentFiles(): array
    {
        return [
            '.env',
            '.env.example',
            '.env.dist',
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
