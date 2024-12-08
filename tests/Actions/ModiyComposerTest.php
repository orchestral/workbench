<?php

namespace Orchestra\Workbench\Tests\Actions;

use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\TestCase;
use Orchestra\Workbench\Actions\ModifyComposer;

use function Orchestra\Testbench\join_paths;

class ModifyComposerTest extends TestCase
{
    /** {@inheritDoc} */
    #[\Override]
    protected function setUp(): void 
    {
        $this->afterApplicationCreated(function () {
            copy(join_paths(__DIR__, 'stubs', 'composer.json'), join_paths(__DIR__, 'tmp', 'composer.json'));
        });

        $this->beforeApplicationDestroyed(function () {
            @unlink(join_paths(__DIR__, 'tmp', 'composer.json'));
        });

        parent::setUp();
    }

    /** @test */
    public function it_can_modify_composer_file()
    {
        $filesystem = new Filesystem;
        $workingPath = join_paths(__DIR__, 'tmp');

        $action = new ModifyComposer($filesystem, $workingPath);

        $this->assertTrue(is_file(join_paths($workingPath, 'composer.json')));
        $this->assertSame('{}'.PHP_EOL, $filesystem->get(join_paths($workingPath, 'composer.json')));

        $action->handle(function (array $content) {
            $content['$schema'] = 'https://getcomposer.org/schema.json';

            return $content;
        });

        $this->assertTrue(is_file(join_paths($workingPath, 'composer.json')));
        $this->assertSame('{
    "$schema": "https://getcomposer.org/schema.json"
}', $filesystem->get(join_paths($workingPath, 'composer.json')));
    }
}