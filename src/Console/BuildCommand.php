<?php

namespace Orchestra\Workbench\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

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
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run builds for workbench';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return Command::SUCCESS;
    }
}
