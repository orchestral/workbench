<?php

namespace Orchestra\Workbench\Recipes;

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Orchestra\Workbench\Contracts\Recipe;
use Symfony\Component\Console\Output\OutputInterface;

class Command implements Recipe
{
    /**
     * Construct a new recipe.
     *
     * @param  string  $command
     * @param  array<string, mixed>  $options
     */
    public function __construct(
        public string $command,
        public array $options = []
    ) {
        //
    }

    /**
     * Run the recipe.
     *
     * @param  \Illuminate\Contracts\Console\Kernel  $kernel
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return void
     */
    public function handle(ConsoleKernel $kernel, OutputInterface $output)
    {
        $kernel->call(
            $this->commandName(), $this->commandOptions(), $output
        );
    }

    /**
     * Get the command name.
     *
     * @return string
     */
    protected function commandName(): string
    {
        return $this->command;
    }

    /**
     * Get the command options.
     *
     * @return array<string, mixed>
     */
    protected function commandOptions(): array
    {
        return $this->options;
    }
}
