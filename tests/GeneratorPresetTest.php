<?php

namespace Orchestra\Workbench\Tests;

use BadMethodCallException;
use Orchestra\Canvas\Core\PresetManager;
use Orchestra\Canvas\Core\Presets\Preset;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class GeneratorPresetTest extends TestCase
{
    use WithWorkbench;

    #[Test]
    public function it_can_be_resolved_and_has_correct_signature()
    {
        $workingPath = realpath(__DIR__.'/../workbench');

        $preset = $this->app[PresetManager::class]->driver('workbench');

        $this->assertInstanceOf(Preset::class, $preset);
        $this->assertSame('workbench', $preset->name());

        $this->assertSame($workingPath, $preset->basePath());
        $this->assertSame($workingPath.DIRECTORY_SEPARATOR.'app', $preset->sourcePath());
        $this->assertSame($workingPath.DIRECTORY_SEPARATOR.'resources', $preset->resourcePath());
        $this->assertSame($workingPath.DIRECTORY_SEPARATOR.'resources/views', $preset->viewPath());
        $this->assertSame($workingPath.DIRECTORY_SEPARATOR.'database/factories', $preset->factoryPath());
        $this->assertSame($workingPath.DIRECTORY_SEPARATOR.'database/migrations', $preset->migrationPath());
        $this->assertSame($workingPath.DIRECTORY_SEPARATOR.'database/seeders', $preset->seederPath());

        $this->assertSame('Workbench\App\\', $preset->rootNamespace());
        $this->assertSame('Workbench\App\Console\\', $preset->commandNamespace());
        $this->assertSame('Workbench\App\Models\\', $preset->modelNamespace());
        $this->assertSame('Workbench\App\Providers\\', $preset->providerNamespace());
        $this->assertSame('Workbench\Database\Factories\\', $preset->factoryNamespace());
        $this->assertSame('Workbench\Database\Seeders\\', $preset->seederNamespace());

        $this->assertFalse($preset->hasCustomStubPath());
        $this->assertSame('Illuminate\Foundation\Auth\User', $preset->userProviderModel());
    }

    #[Test]
    public function it_cant_access_testing_path()
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Generating test is not supported for [workbench] preset');

        $this->app[PresetManager::class]->driver('workbench')->testingPath();
    }

    #[Test]
    public function it_cant_access_testing_namespace()
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Generating test is not supported for [workbench] preset');

        $this->app[PresetManager::class]->driver('workbench')->testingNamespace();
    }
}
