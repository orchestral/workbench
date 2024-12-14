<?php

namespace Orchestra\Workbench\Tests\Console;

class DevToolCommandTest extends CommandTestCase
{
    /**
     * @test
     *
     * @dataProvider environmentFileDataProviders
     */
    public function it_can_run_devtool_command_with_installation(?string $answer, bool $createEnvironmentFile)
    {
        $this->artisan('workbench:devtool', ['--install' => true])
            ->expectsConfirmation('Prefix with `Workbench` namespace?', 'yes')
            ->expectsChoice("Export '.env' file as?", $answer, [
                'Skip exporting .env',
                '.env',
                '.env.example',
                '.env.dist',
            ])->assertSuccessful();

        $this->assertCommandExecutedWithDevTool();
        $this->assertCommandExecutedWithInstall();
        $this->assertFromEnvironmentFileDataProviders($answer, $createEnvironmentFile);
    }

    /**
     * @test
     *
     * @dataProvider environmentFileDataProviders
     */
    public function it_can_run_devtool_command_with_basic_installation(?string $answer, bool $createEnvironmentFile)
    {
        $this->artisan('workbench:devtool', ['--install' => true, '--basic' => true])
            ->expectsConfirmation('Prefix with `Workbench` namespace?', 'yes')
            ->expectsChoice("Export '.env' file as?", $answer, [
                'Skip exporting .env',
                '.env',
                '.env.example',
                '.env.dist',
            ])->assertSuccessful();

        $this->assertCommandExecutedWithDevTool();
        $this->assertCommandExecutedWithBasicInstall();
        $this->assertFromEnvironmentFileDataProviders($answer, $createEnvironmentFile);
    }

    /** @test */
    public function it_can_run_devtool_command_without_installation()
    {
        $this->artisan('workbench:devtool', ['--no-install' => true])
            ->expectsConfirmation('Prefix with `Workbench` namespace?', 'yes')
            ->assertSuccessful();

        $this->assertCommandExecutedWithDevTool();
        $this->assertCommandExecutedWithoutInstall();
    }

    /** @test */
    public function it_can_be_installed_with_no_interaction_options()
    {
        $this->artisan('workbench:devtool', ['--no-install' => true, '--no-interaction' => true])
            ->expectsConfirmation('Prefix with `Workbench` namespace?', 'yes')
            ->assertSuccessful();

        $this->assertCommandExecutedWithDevTool();
        $this->assertCommandExecutedWithoutInstall();
    }
}
