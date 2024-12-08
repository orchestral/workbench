<?php

namespace Orchestra\Workbench\Tests\Actions;

use Orchestra\Testbench\TestCase;
use Orchestra\Workbench\Actions\ModifyComposer;
use RuntimeException;

use function Orchestra\Testbench\join_paths;

/**
 * @requires OS Linux|DAR
 *
 * @group composer
 */
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
        $workingPath = join_paths(__DIR__, 'tmp');

        $action = new ModifyComposer($workingPath);

        $this->assertTrue(is_file(join_paths($workingPath, 'composer.json')));
        $this->assertSame('{}'.PHP_EOL, file_get_contents(join_paths($workingPath, 'composer.json')));

        $action->handle(function (array $content) {
            $content['$schema'] = 'https://getcomposer.org/schema.json';

            return $content;
        });

        $this->assertTrue(is_file(join_paths($workingPath, 'composer.json')));
        $this->assertSame('{
    "$schema": "https://getcomposer.org/schema.json"
}', file_get_contents(join_paths($workingPath, 'composer.json')));
    }

    /** @test */
    public function it_throws_exception_when_composer_file_does_not_exists()
    {
        $workingPath = __DIR__;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Unable to locate `composer.json` file at [%s]', $workingPath));

        $action = new ModifyComposer($workingPath);
        $action->handle(static fn (array $content) => $content);
    }
}
