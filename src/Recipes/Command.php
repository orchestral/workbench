<?php

namespace Orchestra\Workbench\Recipes;

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Orchestra\Workbench\Contracts\Recipe;
use Symfony\Component\Console\Output\OutputInterface;

class Command implements Recipe
{
    /**
     * The command name.
     *
     * @var string
     */
    public $command;

    /**
     * The command options.
     *
     * @var array<string, mixed>
     */
    public $options = [];

    public function __construct(string $command, array $options = [])
    {
        $this->command = $command;
        $this->options = $options;
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
        $kernel->call($this->command, $this->options, $output);
    }
}
