<?php

namespace Orchestra\Workbench\Console\Concerns;

use Illuminate\Filesystem\Filesystem;

trait InteractsWithFiles
{
    /**
     * Replace a given string within a given file.
     */
    protected function replaceInFile(Filesystem $filesystem, array|string $search, array|string $replace, string $path): void
    {
        /** @phpstan-ignore argument.type */
        $filesystem->put($path, str_replace($search, $replace, $filesystem->get($path)));
    }
}
