<?php

namespace Orchestra\Workbench\Tests\Console;

use Orchestra\Testbench\Foundation\Config;

use function Orchestra\Testbench\default_skeleton_path;
use function Orchestra\Testbench\join_paths;

class InstallCommandTest extends CommandTestCase
{
    /**
     * @test
     *
     * @dataProvider environmentFileDataProviders
     */
    public function it_can_run_installation_command_with_devtool(?string $answer, bool $createEnvironmentFile)
    {
        $workingPath = static::stubWorkingPath();

        $this->artisan('workbench:install', ['--devtool' => true, '--no-interaction' => true])
            ->expectsChoice("Export '.env' file as?", $answer, [
                'Skip exporting .env',
                '.env',
                '.env.example',
                '.env.dist',
            ])->assertSuccessful();

        $this->assertFileExists(join_paths($workingPath, 'testbench.yaml'));

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

        $this->assertExecuteInstallWithDevTool();
        $this->assertFromEnvironmentFileDataProviders($answer, $createEnvironmentFile);
    }

    /**
     * @test
     *
     * @dataProvider environmentFileDataProviders
     */
    public function it_can_run_installation_command_without_devtool(?string $answer, bool $createEnvironmentFile)
    {
        $workingPath = static::stubWorkingPath();

        $this->artisan('workbench:install', ['--no-devtool' => true, '--no-interaction' => true])
            ->expectsChoice("Export '.env' file as?", $answer, [
                'Skip exporting .env',
                '.env',
                '.env.example',
                '.env.dist',
            ])->assertSuccessful();

        $this->assertFileExists(join_paths($workingPath, 'testbench.yaml'));

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

        $this->assertExecuteInstallWithoutDevTool();
        $this->assertFromEnvironmentFileDataProviders($answer, $createEnvironmentFile);
    }

    /**
     * @test
     *
     * @dataProvider environmentFileDataProviders
     */
    public function it_can_run_basic_installation_command_without_devtool(?string $answer, bool $createEnvironmentFile)
    {
        $workingPath = static::stubWorkingPath();

        $this->artisan('workbench:install', ['--basic' => true, '--no-devtool' => true, '--no-interaction' => true])
            ->expectsChoice("Export '.env' file as?", $answer, [
                'Skip exporting .env',
                '.env',
                '.env.example',
                '.env.dist',
            ])->assertSuccessful();

        $this->assertFileExists(join_paths($workingPath, 'testbench.yaml'));

        $config = Config::loadFromYaml($workingPath);

        $this->assertSame(default_skeleton_path(), $config['laravel']);
        $this->assertSame([
            \Workbench\Database\Seeders\DatabaseSeeder::class,
        ], $config->seeders);
        $this->assertSame([], $config->getWorkbenchAttributes()['build']);
        $this->assertSame([], $config->getWorkbenchAttributes()['assets']);

        $this->assertExecuteInstallWithoutDevTool();
        $this->assertFromEnvironmentFileDataProviders($answer, $createEnvironmentFile);
    }
}
