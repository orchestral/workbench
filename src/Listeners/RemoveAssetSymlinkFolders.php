<?php

namespace Orchestra\Workbench\Listeners;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Orchestra\Testbench\Foundation\Events\ServeCommandEnded;
use Orchestra\Workbench\Workbench;

class RemoveAssetSymlinkFolders
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
     * @param  \Orchestra\Testbench\Foundation\Events\ServeCommandEnded  $event
     * @return void
     */
    public function handle(ServeCommandEnded $event): void
    {
        /** @var array<int, array{from: string, to: string}> $sync */
        $sync = Workbench::config('sync');

        Collection::make($sync)
            ->map(function ($pair) {
                /** @var string $from */
                $from = Workbench::packagePath($pair['from']);

                /** @var string $to */
                $to = Workbench::laravelPath($pair['to']);

                if (! is_link($to)) {
                    return null;
                }

                return $to;
            })->filter()
            ->each(function ($linkPath) {
                /** @phpstan-ignore-next-line */
                $this->files->delete($linkPath);
            });
    }
}
