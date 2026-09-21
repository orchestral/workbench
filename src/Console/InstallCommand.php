<?php

namespace Orchestra\Workbench\Console;

use Composer\InstalledVersions;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Orchestra\Testbench\Foundation\Console\Actions\EnsureDirectoryExists;
use Orchestra\Testbench\Foundation\Console\Actions\GeneratesFile;
use Orchestra\Testbench\Foundation\Console\Actions\RunCommand;
use Orchestra\Workbench\StubRegistrar;
use Orchestra\Workbench\Workbench;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Orchestra\Sidekick\Filesystem\join_paths;
use function Orchestra\Testbench\package_path;

#[AsCommand(name: 'workbench:install', description: 'Setup Workbench for package development')]
class InstallCommand extends Command implements PromptsForMissingInput
{
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
        /** @var bool $pretending */
        $pretending = $this->option('pretend');

        $runCommand = (new RunCommand(
            console: $this,
            components: $this->components,
            pretending: $pretending,
        ));

        if (! $this->option('skip-devtool')) {
            $devtool = match (true) {
                \is_bool($this->option('devtool')) => $this->option('devtool'),
                default => $this->components->confirm('Install Workbench DevTool?', true),
            };

            if ($devtool === true) {
                $runCommand->handle('workbench:devtool', [
                    '--force' => $this->option('force'),
                    '--no-install' => true,
                    '--basic' => $this->option('basic'),
                ]);
            }
        }

        $workingPath = package_path();

        $this->copyTestbenchConfigurationFile($filesystem, $workingPath, $pretending);
        $this->copyTestbenchDotEnvFile($filesystem, $workingPath, $pretending);

        $this->replaceDefaultLaravelSkeletonInTestbenchConfigurationFile($filesystem, $workingPath, $pretending);

        $runCommand->handle('workbench:create-sqlite-db', [
            '--force' => true,
        ]);

        return Command::SUCCESS;
    }

    /**
     * Copy the "testbench.yaml" file.
     */
    protected function copyTestbenchConfigurationFile(Filesystem $filesystem, string $workingPath, bool $pretending): void
    {
        $from = ! \is_null(static::$configurationBaseFile)
            ? (string) realpath(static::$configurationBaseFile)
            : (string) Workbench::stubFile($this->option('basic') === true ? 'config.basic' : 'config');

        $to = join_paths($workingPath, 'testbench.yaml');

        (new GeneratesFile(
            filesystem: $filesystem,
            components: $this->components,
            force: (bool) $this->option('force'),
            pretending: $pretending,
        ))->handle($from, $to);

        if ($pretending === false) {
            StubRegistrar::replaceInFile($filesystem, $to);
        }
    }

    /**
     * Copy the ".env" file.
     */
    protected function copyTestbenchDotEnvFile(Filesystem $filesystem, string $workingPath, bool $pretending): void
    {
        $workbenchWorkingPath = join_paths($workingPath, 'workbench');

        $from = $this->laravel->basePath('.env.example');

        if (! $filesystem->isFile($from)) {
            return;
        }

        /** @var \Illuminate\Support\Collection<int, string> $choices */
        $choices = (new Collection($this->environmentFiles()))
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
            ? select(
                "Export '.env' file as?",
                $choices->prepend('Skip exporting .env'), // @phpstan-ignore argument.type
            ) : null;

        if (\in_array($targetEnvironmentFile, [null, 'Skip exporting .env'])) {
            return;
        }

        (new EnsureDirectoryExists(
            filesystem: $filesystem,
            pretending: $pretending,
        ))->handle([
            $workbenchWorkingPath,
        ]);

        $this->generateSeparateEnvironmentFileForTestbenchDusk($filesystem, $workbenchWorkingPath, $targetEnvironmentFile);

        (new GeneratesFile(
            filesystem: $filesystem,
            components: $this->components,
            force: (bool) $this->option('force'),
            pretending: $pretending,
        ))->handle(
            $from,
            join_paths($workbenchWorkingPath, $targetEnvironmentFile)
        );

        (new GeneratesFile(
            filesystem: $filesystem,
            force: (bool) $this->option('force'),
            pretending: $pretending,
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
    protected function replaceDefaultLaravelSkeletonInTestbenchConfigurationFile(
        Filesystem $filesystem,
        string $workingPath,
        bool $pretending,
    ): void {
        if ($this->hasTestbenchDusk === false || $pretending === true) {
            return;
        }

        $filesystem->replaceInFile(["laravel: '@testbench'"], ["laravel: '@testbench-dusk'"], join_paths($workingPath, 'testbench.yaml'));
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
                pretending: (bool) $this->option('pretend'),
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
     * Prompt the user for any missing arguments.
     *
     * @return void
     */
    protected function promptForMissingArguments(InputInterface $input, OutputInterface $output)
    {
        $devtool = null;

        if ($input->getOption('skip-devtool') === true) {
            $devtool = false;
        } elseif (\is_null($input->getOption('devtool'))) {
            $devtool = confirm('Run Workbench DevTool installation?', true);
        }

        if (! \is_null($devtool)) {
            $input->setOption('devtool', $devtool);
        }
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
            ['pretend', null, InputOption::VALUE_NONE, 'Outputs the operations but will not execute anything'],

            /** @deprecated */
            ['skip-devtool', null, InputOption::VALUE_NONE, 'Skipped DevTool installation (deprecated)'],
        ];
    }
}
