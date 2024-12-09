<?php 

namespace Orchestra\Workbench\Tests\Console;

use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\Foundation\TestbenchServiceProvider;
use Orchestra\Workbench\WorkbenchServiceProvider;

use function Orchestra\Testbench\join_paths;

abstract class CommandTestCase extends \Orchestra\Testbench\TestCase
{
    /** {@inheritDoc} */
    #[\Override]
    protected function setUp(): void
    {
        $filesystem = new Filesystem;
        $workingPath = static::stubWorkingPath();

        $this->beforeApplicationDestroyed(function () use ($filesystem, $workingPath) {
            $filesystem->deleteDirectory($workingPath);
            unset($_ENV['TESTBENCH_WORKING_PATH']);
        });

        $_ENV['TESTBENCH_WORKING_PATH'] = $workingPath;
        $filesystem->ensureDirectoryExists($workingPath);

        parent::setUp();
    }

    /** {@inheritDoc} */
    #[\Override]
    protected function getPackageProviders($app)
    {
        return [
            TestbenchServiceProvider::class,
            WorkbenchServiceProvider::class,
        ];
    }

    /**
     * Assert from `environmentFileDataProviders`
     */
    protected function assertFromEnvironmentFileDataProviders(?string $answer, bool $createEnvironmentFile): void
    {
        $workingPath = static::stubWorkingPath();

        if (is_null($answer) || $createEnvironmentFile === false) {
            collect(['.env', '.env.example', '.env.dist'])
                ->each(function ($file) use ($workingPath) {
                    $this->assertFalse(is_file(join_paths($workingPath, 'workbench', $file)));
                });
        } else {
            $this->assertTrue(is_file(join_paths($workingPath, 'workbench', $answer)));
        }
    }

    /**
     * `environmentFileDataProviders` data provider.
     */
    public static function environmentFileDataProviders()
    {
        yield ['Skip exporting .env', false];
        yield ['.env', true];
        yield ['.env.example', true];
        yield ['.env.dist', true];
    }

    /**
     * Get the stub working path.
     */
    protected static function stubWorkingPath(): string
    {
        return join_paths(__DIR__, \sprintf('%s_stubs', class_basename(static::class)));
    }
}