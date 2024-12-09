<?php

namespace Orchestra\Workbench\Tests\Console;

use Illuminate\Filesystem\Filesystem;
use Orchestra\Canvas\LaravelServiceProvider;
use Orchestra\Testbench\Foundation\TestbenchServiceProvider;
use Orchestra\Workbench\Workbench;
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
            Workbench::flush();
        });

        $_ENV['TESTBENCH_WORKING_PATH'] = $workingPath;
        $filesystem->ensureDirectoryExists($workingPath);
        $filesystem->copy(join_paths(__DIR__, 'stubs', 'composer.json'), join_paths($workingPath, 'composer.json'));

        parent::setUp();
    }

    /** {@inheritDoc} */
    #[\Override]
    protected function getPackageProviders($app)
    {
        return [
            TestbenchServiceProvider::class,
            WorkbenchServiceProvider::class,
            LaravelServiceProvider::class,
        ];
    }

    /**
     * Assert `workbench:install` command executed with `--devtool`
     */
    protected function assertExecuteInstallWithDevTool(): void
    {
        $workingPath = static::stubWorkingPath();

        $this->assertDirectoryExists(join_paths($workingPath, 'workbench', 'app', 'Models'));
        $this->assertDirectoryExists(join_paths($workingPath, 'workbench', 'app', 'Providers'));
        $this->assertDirectoryExists(join_paths($workingPath, 'workbench', 'database', 'factories'));
        $this->assertDirectoryExists(join_paths($workingPath, 'workbench', 'database', 'seeders'));
    }

    /**
     * Assert `workbench:install` command executed with `--no-devtool`
     */
    protected function assertExecuteInstallWithoutDevTool(): void
    {
        $workingPath = static::stubWorkingPath();

        $this->assertDirectoryDoesNotExist(join_paths($workingPath, 'workbench', 'app'));
        $this->assertDirectoryDoesNotExist(join_paths($workingPath, 'workbench', 'database'));
    }

    /**
     * Assert `workbench:devtool` command executed with `--install`
     */
    protected function assertExecuteDevToolWithInstall(): void
    {
        $this->markTestIncomplete('Implements '.__METHOD__);
    }

    /**
     * Assert `workbench:devtool` command executed with `--no-install`
     */
    protected function assertExecuteDevToolWithoutInstall(): void
    {
        $this->markTestIncomplete('Implements '.__METHOD__);
    }

    /**
     * Assert from `environmentFileDataProviders`
     */
    protected function assertFromEnvironmentFileDataProviders(?string $answer, bool $createEnvironmentFile): void
    {
        $workingPath = static::stubWorkingPath();

        if (\is_null($answer) || $createEnvironmentFile === false) {
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
