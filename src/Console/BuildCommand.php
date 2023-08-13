<?php

namespace Orchestra\Workbench\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Collection;
use function Orchestra\Testbench\workbench;
use Symfony\Component\Console\Attribute\AsCommand;

/**
 * @phpstan-import-type TWorkbenchConfig from \Orchestra\Testbench\Foundation\Config
 */
#[AsCommand(name: 'workbench:build', description: 'Run builds for workbench')]
class BuildCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workbench:build';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ConsoleKernel $kernel)
    {
        /** @var TWorkbenchConfig $workbench */
        $workbench = workbench();

        $recipes = $this->laravel->make('workbench.recipe');

        Collection::make($workbench['build'])
            ->each(function (string $build) use ($kernel, $recipes) {
                $recipes->action($build)->handle($kernel, $this->output);
            });

        return Command::SUCCESS;
    }
}
