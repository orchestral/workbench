<?php

namespace Orchestra\Workbench\Actions;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;

class DumpComposerAutoloads
{
    /**
     * Construct a new action.
     */
    public function __construct(
        protected Filesystem $files,
        public readonly string $workingPath
    ) {}

    /**
     * Handle the action.
     *
     * @param  callable(array):array  $callback
     */
    public function handle(callable $callback): void
    {
        app(
            Composer::class,
            ['files' => $this->files, 'workingPath' => $this->workingPath]
        )->dumpAutoloads();
    }
}
