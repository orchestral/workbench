<?php

namespace Orchestra\Workbench\Tests;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;
use Orchestra\Workbench\Contracts\RecipeManager as RecipeManagerContract;
use Orchestra\Workbench\RecipeManager;
use Orchestra\Workbench\Recipes\AssetPublishCommand;
use Orchestra\Workbench\Recipes\Command;
use Orchestra\Workbench\WorkbenchServiceProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Test;

class RecipeManagerTest extends TestCase
{
    use WithWorkbench;

    /** {@inheritDoc} */
    #[\Override]
    protected function getPackageProviders($app)
    {
        return array_merge(parent::getPackageProviders($app), [
            WorkbenchServiceProvider::class,
        ]);
    }

    #[Test]
    #[Depends('it_can_check_for_valid_commands')]
    public function it_can_be_resolved()
    {
        tap($this->app->make(RecipeManagerContract::class), function ($manager) {
            $this->assertInstanceOf(RecipeManager::class, $manager);
            $this->assertInstanceOf(RecipeManagerContract::class, $manager);
            $this->assertSame('asset-publish', $manager->getDefaultDriver());

            $this->assertInstanceOf(AssetPublishCommand::class, $manager->command('asset-publish'));

            tap($manager->command('create-sqlite-db'), function ($recipe) {
                $this->assertInstanceOf(Command::class, $recipe);
                $this->assertSame('workbench:create-sqlite-db', $recipe->command);
                $this->assertSame([], $recipe->options);
            });

            tap($manager->command('drop-sqlite-db'), function ($recipe) {
                $this->assertInstanceOf(Command::class, $recipe);
                $this->assertSame('workbench:drop-sqlite-db', $recipe->command);
                $this->assertSame([], $recipe->options);
            });

            tap($manager->command('sync-skeleton'), function ($recipe) {
                $this->assertInstanceOf(Command::class, $recipe);
                $this->assertSame('workbench:sync-skeleton', $recipe->command);
                $this->assertSame([], $recipe->options);
            });

            tap($manager->command('purge-skeleton'), function ($recipe) {
                $this->assertInstanceOf(Command::class, $recipe);
                $this->assertSame('workbench:purge-skeleton', $recipe->command);
                $this->assertSame([], $recipe->options);
            });
        });
    }

    #[Test]
    #[DataProvider('validCommands')]
    public function it_can_check_for_valid_commands(string $command)
    {
        tap($this->app->make(RecipeManagerContract::class), function ($manager) use ($command) {
            $this->assertTrue($manager->hasCommand($command));
        });
    }

    #[Test]
    #[DataProvider('invalidCommands')]
    public function it_can_check_for_invalid_commands(string $command)
    {
        tap($this->app->make(RecipeManagerContract::class), function ($manager) use ($command) {
            $this->assertFalse($manager->hasCommand($command));
        });
    }

    #[Test]
    public function it_can_check_for_valid_custom_commands()
    {
        tap($this->app->make(RecipeManagerContract::class), function ($manager) {
            $manager->extend('foo-asset-publish', fn () => new AssetPublishCommand);

            $this->assertTrue($manager->hasCommand('foo-asset-publish'));
        });
    }

    #[Test]
    public function it_can_check_for_invalid_custom_commands()
    {
        tap($this->app->make(RecipeManagerContract::class), function ($manager) {
            $manager->extend('foo-asset-publish', fn () => new AssetPublishCommand);

            $this->assertFalse($manager->hasCommand('foobar-asset-publish'));
        });
    }

    #[Test]
    public function it_can_generate_anonymous_command()
    {
        tap($this->app->make(RecipeManagerContract::class), function ($manager) {
            $recipe = $manager->commandUsing('migrate:refresh', ['--seed' => true]);

            $this->assertSame('migrate:refresh', $recipe->command);
            $this->assertSame(['--seed' => true], $recipe->options);
        });
    }

    public static function validCommands()
    {
        yield ['asset-publish'];
        yield ['create-sqlite-db'];
        yield ['drop-sqlite-db'];
        yield ['sync-skeleton'];
        yield ['purge-skeleton'];
    }

    public static function invalidCommands()
    {
        yield ['laravel-serve'];
    }
}
