<?php

namespace Orchestra\Workbench\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Orchestra\Testbench\Foundation\Console\Actions\GeneratesFile;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function Illuminate\Filesystem\join_paths;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Orchestra\Testbench\package_path;

#[AsCommand(name: 'workbench:install', description: 'Setup Workbench for package development')]
class InstallCommand extends Command implements PromptsForMissingInput
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
        if ($this->option('devtool') === true) {
            $this->call('workbench:devtool', [
                '--force' => $this->option('force'),
                '--no-install' => true,
            ]);
        }

        $workingPath = package_path();

        $this->copyTestbenchConfigurationFile($filesystem, $workingPath);
        $this->copyTestbenchDotEnvFile($filesystem, $workingPath);
        $this->prepareWorkbenchDirectories($filesystem, $workingPath);

        $this->call('workbench:create-sqlite-db', ['--force' => true]);

        return Command::SUCCESS;
    }

    /**
     * Prepare workbench directories.
     */
    protected function prepareWorkbenchDirectories(Filesystem $filesystem, string $workingPath): void
    {
        $workbenchWorkingPath = join_paths($workingPath, 'workbench');

        foreach (['app' => true, 'providers' => false] as $bootstrap => $default) {
            if (! confirm("Generate `workbench/bootstrap/{$bootstrap}.php` file?", default: $default)) {
                continue;
            }

            (new GeneratesFile(
                filesystem: $filesystem,
                components: $this->components,
                force: (bool) $this->option('force'),
            ))->handle(
                (string) realpath(join_paths(__DIR__, 'stubs', 'bootstrap', "{$bootstrap}.php")),
                join_paths($workbenchWorkingPath, 'bootstrap', "{$bootstrap}.php")
            );
        }
    }

    /**
     * Copy the "testbench.yaml" file.
     */
    protected function copyTestbenchConfigurationFile(Filesystem $filesystem, string $workingPath): void
    {
        $from = (string) realpath(static::$configurationBaseFile ?? join_paths(__DIR__, 'stubs', 'testbench.yaml'));
        $to = join_paths($workingPath, 'testbench.yaml');

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
        $workbenchWorkingPath = join_paths($workingPath, 'workbench');

        $from = $this->laravel->basePath('.env.example');

        if (! $filesystem->exists($from)) {
            return;
        }

        $choices = Collection::make($this->environmentFiles())
            ->reject(static fn ($file) => $filesystem->exists(join_paths($workbenchWorkingPath, $file)))
            ->values()
            ->prepend('Skip exporting .env')
            ->all();

        if (! $this->option('force') && empty($choices)) {
            $this->components->twoColumnDetail(
                'File [.env] already exists', '<fg=yellow;options=bold>SKIPPED</>'
            );

            return;
        }

        /** @var string $choice */
        $choice = select("Export '.env' file as?", $choices);

        if ($choice === 'Skip exporting .env') {
            return;
        }

        $to = join_paths($workbenchWorkingPath, $choice);

        (new GeneratesFile(
            filesystem: $filesystem,
            components: $this->components,
            force: (bool) $this->option('force'),
        ))->handle($from, $to);

        (new GeneratesFile(
            filesystem: $filesystem,
            force: (bool) $this->option('force'),
        ))->handle(
            (string) realpath(join_paths(__DIR__, 'stubs', 'workbench.gitignore')),
            join_paths($workbenchWorkingPath, '.gitignore')
        );
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

            /** @deprecated */
            ['skip-devtool', null, InputOption::VALUE_NONE, 'Skipped DevTool installation (deprecated)'],
        ];
    }
}
