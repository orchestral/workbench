<?php

namespace Orchestra\Workbench\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Collection;
use Orchestra\Workbench\Contracts\RecipeManager;
use Orchestra\Workbench\Workbench;
use Symfony\Component\Console\Attribute\AsCommand;

/**
 * @phpstan-import-type TWorkbenchConfig from \Orchestra\Testbench\Foundation\Config
 */
#[AsCommand(name: 'workbench:build', description: 'Run builds for workbench')]
class BuildCommand extends Command
{
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ConsoleKernel $kernel, RecipeManager $recipes)
    {
        $commands = Collection::make($kernel->all())
            ->keys()
            ->filter(static fn ($command) => \is_string($command))
            ->mapWithKeys(static fn (string $command) => [str_replace(':', '-', $command) => $command]);

        /** @var array<int, string>|array<string, array<string, mixed>> $build */
        $build = Workbench::config('build');

        Collection::make($build)
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

                return [$name => Collection::make($options)->mapWithKeys(static fn ($value, $key) => ['--'.ltrim($key, '-') => $value])->all()];
            })
            ->each(function (array $options, string $name) use ($kernel, $recipes, $commands) {
                if ($recipes->hasCommand($name)) {
                    $recipes->command($name)->handle($kernel, $this->output);

                    return;
                }

                $command = $commands->get($name) ?? $commands->first(static fn ($commandName) => $name === $commandName);

                if (! \is_null($command)) {
                    $recipes->commandUsing($command, $options)->handle($kernel, $this->output);
                }
            });

        return Command::SUCCESS;
    }
}
