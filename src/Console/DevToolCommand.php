<?php

namespace Orchestra\Workbench\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Orchestra\Testbench\Foundation\Console\Actions\GeneratesFile;
use Symfony\Component\Console\Attribute\AsCommand;

use function Illuminate\Filesystem\join_paths;
use function Laravel\Prompts\select;
use function Orchestra\Testbench\package_path;

#[AsCommand(name: 'workbench:devtool', description: 'Configure Workbench for package development')]
class DevToolCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workbench:devtool {--force : Overwrite any existing files}';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Filesystem $filesystem)
    {
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
        $from = (string) realpath(join_paths(__DIR__, 'stubs', 'testbench.yaml'));
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
            ->reject(static function ($file) use ($filesystem, $workbenchWorkingPath) {
                return $filesystem->exists(join_paths($workbenchWorkingPath, $file));
            })->values()
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
        ))->handle((string) realpath(join_paths(__DIR__, 'stubs', 'workbench.gitignore')), join_paths($workbenchWorkingPath, '.gitignore'));
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
