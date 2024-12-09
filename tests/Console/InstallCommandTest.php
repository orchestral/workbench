<?php 

namespace Orchestra\Workbench\Tests\Console;

use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\Foundation\Config;
use Orchestra\Testbench\Foundation\TestbenchServiceProvider;
use Orchestra\Testbench\TestCase;
use Orchestra\Workbench\WorkbenchServiceProvider;

use function Orchestra\Testbench\artisan;
use function Orchestra\Testbench\default_skeleton_path;
use function Orchestra\Testbench\join_paths;

class InstallCommandTest extends TestCase
{
    /**
     * Stub
     */
    protected ?string $directory = null;

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

    /** @test */
    public function it_can_run_installation_command_without_devtool()
    {
        $workingPath = static::stubWorkingPath();

        $this->withoutMockingConsoleOutput();

        artisan($this, 'workbench:install', ['--no-devtool' => true, '--no-interaction' => true]);

        $this->assertTrue(is_file(join_paths($workingPath, 'testbench.yaml')));

        $config = Config::loadFromYaml($workingPath);

        $this->assertSame(default_skeleton_path(), $config['laravel']);
        $this->assertFalse($config->seeders);
        $this->assertSame([
            'asset-publish',
            'create-sqlite-db',
            'db-wipe', 
            ['migrate-fresh' => [
                '--seed' => true,
                '--seeder' => \Workbench\Database\Seeders\DatabaseSeeder::class,
            ]],
        ], $config->getWorkbenchAttributes()['build']);
        $this->assertSame([
            'laravel-assets',
        ], $config->getWorkbenchAttributes()['assets']);
    }

    /** @test */
    public function it_can_run_basic_installation_command_without_devtool()
    {
        $workingPath = static::stubWorkingPath();

        $this->withoutMockingConsoleOutput();

        artisan($this, 'workbench:install', ['--basic' => true, '--no-devtool' => true, '--no-interaction' => true]);

        $this->assertTrue(is_file(join_paths($workingPath, 'testbench.yaml')));

        $config = Config::loadFromYaml($workingPath);

        $this->assertSame(default_skeleton_path(), $config['laravel']);
        $this->assertSame([
            \Workbench\Database\Seeders\DatabaseSeeder::class,
        ], $config->seeders);
        $this->assertSame([], $config->getWorkbenchAttributes()['build']);
        $this->assertSame([], $config->getWorkbenchAttributes()['assets']);
    }

    protected static function stubWorkingPath(): string 
    {
        return join_paths(__DIR__, sprintf('%s_stubs', class_basename(static::class)));
    }
}