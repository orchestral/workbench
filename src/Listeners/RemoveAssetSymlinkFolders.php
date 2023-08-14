<?php

namespace Orchestra\Workbench\Listeners;

use Orchestra\Testbench\Foundation\Events\ServeCommandEnded;

class RemoveAssetSymlinkFolders
{
    /**
     * Handle the event.
     *
     * @param  \Orchestra\Testbench\Foundation\Events\ServeCommandEnded  $event
     * @return void
     */
    public function handle(ServeCommandEnded $event): void
    {
        $event->components->task('Ended');
        // Access the order using $event->order...
    }
}
