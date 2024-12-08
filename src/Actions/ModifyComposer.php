<?php

namespace Orchestra\Workbench\Actions;

use Illuminate\Filesystem\Filesystem;
use RuntimeException;

class ModifyComposer
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
        $composerFile = "{$this->workingPath}/composer.json";

        if (! file_exists($composerFile)) {
            throw new RuntimeException("Unable to locate `composer.json` file at [{$this->workingPath}].");
        }

        $composer = json_decode((string) $this->files->get($composerFile), true, 512, JSON_THROW_ON_ERROR);

        $composer = \call_user_func($callback, $composer);

        $this->files->put(
            $composerFile,
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)
        );
    }
}
