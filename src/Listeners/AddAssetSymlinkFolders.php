<?php

namespace Orchestra\Workbench\Listeners;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Orchestra\Testbench\Foundation\Events\ServeCommandStarted;
use Orchestra\Workbench\Workbench;

class AddAssetSymlinkFolders
{
    /**
     * Construct a new event listener.
     *
     * @param  \Orchestra\Testbench\Contracts\Config  $config
     */
    public function __construct(
        public ConfigContract $config,
        public Filesystem $files
    ) {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Orchestra\Testbench\Foundation\Events\ServeCommandStarted  $event
     * @return void
     */
    public function handle(ServeCommandStarted $event): void
    {
        /** @var array<int, array{from: string, to: string}> $sync */
        $sync = Workbench::config('sync');

        Collection::make($sync)
            ->map(function ($pair) {
                /** @var string $from */
                $from = Workbench::packagePath($pair['from']);

                /** @var string $to */
                $to = Workbench::laravelPath($pair['to']);

                if (! $this->files->isDirectory($from)) {
                    return null;
                }

                return ['from' => $from, 'to' => $to];
            })->filter()
            ->each(function ($pair) {
                /** @phpstan-ignore-next-line */
                $this->files->link($pair['from'], $pair['to']);
            });
    }
}
