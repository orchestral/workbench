<?php

namespace Orchestra\Workbench\Tests\Actions;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Orchestra\Workbench\Actions\WriteEnvironmentVariables;

class WriteEnvironmentVariablesTest extends TestCase
{
    /**
     * @test
     *
     * @testWith [false]
     *           [null]
     */
    public function it_throws_exception_when_env_file_is_not_available(mixed $filename)
    {
        $this->expectException(FileNotFoundException::class);

        $filesystem = new Filesystem;

        $action = new WriteEnvironmentVariables($filesystem, $filename);

        $action->handle(['APP_NAME' => 'Laravel']);
    }
}
