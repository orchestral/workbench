<?php

namespace Orchestra\Workbench\Tests\Console;

use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use function Orchestra\Testbench\default_skeleton_path;
use function Orchestra\Testbench\join_paths;

class InstallCommandTest extends CommandTestCase
{
    #[Test]
    #[DataProvider('environmentFileDataProviders')]
    public function it_can_run_installation_command_with_devtool(?string $answer, bool $createEnvironmentFile)
    {
        $this->artisan('workbench:install', ['--devtool' => true])
            ->expectsConfirmation('Prefix with `Workbench` namespace?', answer: 'yes')
            ->expectsChoice("Export '.env' file as?", $answer, [
                'Skip exporting .env',
                '.env',
                '.env.example',
                '.env.dist',
            ])
            ->expectsConfirmation('Generate `workbench/bootstrap/app.php` file?', answer: 'no')
            ->expectsConfirmation('Generate `workbench/bootstrap/providers.php` file?', answer: 'no')
            ->assertSuccessful();

        $this->assertCommandExecutedWithInstall();
        $this->assertCommandExecutedWithDevTool();
        $this->assertFromEnvironmentFileDataProviders($answer, $createEnvironmentFile);
    }

    #[Test]
    #[DataProvider('environmentFileDataProviders')]
    public function it_can_run_installation_command_with_devtool_without_workbench_prefix(?string $answer, bool $createEnvironmentFile)
    {
        $this->artisan('workbench:install', ['--devtool' => true])
            ->expectsConfirmation('Prefix with `Workbench` namespace?', answer: 'no')
            ->expectsChoice("Export '.env' file as?", $answer, [
                'Skip exporting .env',
                '.env',
                '.env.example',
                '.env.dist',
            ])
            ->expectsConfirmation('Generate `workbench/bootstrap/app.php` file?', answer: 'no')
            ->expectsConfirmation('Generate `workbench/bootstrap/providers.php` file?', answer: 'no')
            ->assertSuccessful();

        $this->assertCommandExecutedWithInstall(prefix: false);
        $this->assertCommandExecutedWithDevTool(prefix: false);
        $this->assertFromEnvironmentFileDataProviders($answer, $createEnvironmentFile);
    }

    #[Test]
    #[DataProvider('environmentFileDataProviders')]
    public function it_can_run_installation_command_without_devtool(?string $answer, bool $createEnvironmentFile)
    {
        $this->artisan('workbench:install', ['--no-devtool' => true])
            ->expectsChoice("Export '.env' file as?", $answer, [
                'Skip exporting .env',
                '.env',
                '.env.example',
                '.env.dist',
            ])
            ->expectsConfirmation('Generate `workbench/bootstrap/app.php` file?', answer: 'no')
            ->expectsConfirmation('Generate `workbench/bootstrap/providers.php` file?', answer: 'no')
            ->assertSuccessful();

        $this->assertCommandExecutedWithInstall();
        $this->assertCommandExecutedWithoutDevTool();
        $this->assertFromEnvironmentFileDataProviders($answer, $createEnvironmentFile);
    }

    #[Test]
    #[DataProvider('environmentFileDataProviders')]
    public function it_can_run_basic_installation_command_without_devtool(?string $answer, bool $createEnvironmentFile)
    {
        $this->artisan('workbench:install', ['--basic' => true, '--no-devtool' => true])
            ->expectsChoice("Export '.env' file as?", $answer, [
                'Skip exporting .env',
                '.env',
                '.env.example',
                '.env.dist',
            ])
            ->expectsConfirmation('Generate `workbench/bootstrap/app.php` file?', answer: 'no')
            ->expectsConfirmation('Generate `workbench/bootstrap/providers.php` file?', answer: 'no')
            ->assertSuccessful();

        $this->assertCommandExecutedWithBasicInstall();
        $this->assertCommandExecutedWithoutDevTool();
        $this->assertFromEnvironmentFileDataProviders($answer, $createEnvironmentFile);
    }

    #[Test]
    public function it_can_ignore_generating_environment_file_if_it_already_exists()
    {
        $filesystem = new Filesystem;
        $workingPath = static::stubWorkingPath();
        $environmentFiles = collect(['.env', '.env.example', '.env.dist']);

        $filesystem->ensureDirectoryExists(join_paths($workingPath, 'workbench'));

        $environmentFiles->each(function ($env) use ($filesystem, $workingPath) {
            $filesystem->put(join_paths($workingPath, 'workbench', $env), '');
        });

        $this->artisan('workbench:install', ['--basic' => true, '--no-devtool' => true])
            ->expectsOutputToContain('File [.env] already exists')
            ->expectsConfirmation('Generate `workbench/bootstrap/app.php` file?', answer: 'no')
            ->expectsConfirmation('Generate `workbench/bootstrap/providers.php` file?', answer: 'no')
            ->assertSuccessful();

        $environmentFiles->each(function ($env) use ($workingPath) {
            $this->assertFileNotEquals(join_paths($workingPath, 'workbench', $env), default_skeleton_path('.env.example'));
        });

        $this->assertCommandExecutedWithBasicInstall();
        $this->assertCommandExecutedWithoutDevTool();
    }

    #[Test]
    public function it_can_be_installed_with_no_interaction_options()
    {
        $this->artisan('workbench:install', ['--basic' => true, '--no-devtool' => true, '--no-interaction' => true])
            ->assertSuccessful();

        $this->assertCommandExecutedWithBasicInstall();
        $this->assertCommandExecutedWithoutDevTool();
    }

    #[Test]
    public function it_can_be_installed_with_prompt_for_missing_arguments()
    {
        $this->artisan('workbench:install')
            ->expectsConfirmation('Run Workbench DevTool installation?', false)
            ->expectsChoice("Export '.env' file as?", 'Skip exporting .env', [
                'Skip exporting .env',
                '.env',
                '.env.example',
                '.env.dist',
            ])
            ->expectsConfirmation('Generate `workbench/bootstrap/app.php` file?', answer: 'no')
            ->expectsConfirmation('Generate `workbench/bootstrap/providers.php` file?', answer: 'no')
            ->assertSuccessful();

        $this->assertCommandExecutedWithInstall();
        $this->assertCommandExecutedWithoutDevTool();
    }
}
