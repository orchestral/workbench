<?php

namespace Orchestra\Workbench\Listeners;

use Illuminate\Filesystem\Filesystem;
use Orchestra\Workbench\Events\InstallEnded;
use Orchestra\Workbench\Workbench;

class UpdatesUserFactoryClassName
{
    /**
     * Construct a new event listener.
     */
    public function __construct(
        public Filesystem $files
    ) {
        //
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(InstallEnded $event)
    {
        if ($this->files->exists(Workbench::path(['database', 'factories', 'UserFactory.php']))) {
            $this->files->replaceInFile([
                'use Orchestra\Testbench\Factories\UserFactory;',
            ], [
                'use Workbench\Database\Factories\UserFactory;',
            ], Workbench::path(['database', 'seeders', 'DatabaseSeeder.php']));
        }
    }
}
