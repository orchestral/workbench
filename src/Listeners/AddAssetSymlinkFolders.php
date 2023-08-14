<?php

namespace Orchestra\Workbench\Listeners;

use Orchestra\Testbench\Foundation\Events\ServeCommandStarted;

class AddAssetSymlinkFolders
{
    /**
     * Handle the event.
     *
     * @param  \Orchestra\Testbench\Foundation\Events\ServeCommandStarted  $event
     * @return void
     */
    public function handle(ServeCommandStarted $event): void
    {
        $event->components->task('Started');
        // Access the order using $event->order...
    }
}
