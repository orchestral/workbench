<?php

namespace Orchestra\Workbench\Tests;

use BadMethodCallException;
use Illuminate\Console\Generators\PresetManager;
use Illuminate\Console\Generators\Presets\Preset;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Orchestra\Testbench\TestCase;
use Orchestra\Workbench\Workbench;

class GeneratorPresetTest extends TestCase
{
    use WithWorkbench;

    /** @test */
    public function it_can_be_resolved_and_has_correct_signature()
    {
        $workingPath = dirname(__DIR__).'/workbench';

        $preset = $this->app[PresetManager::class]->driver('workbench');

        $this->assertInstanceOf(Preset::class, $preset);
        $this->assertSame('workbench', $preset->name());

        $this->assertSame($workingPath, $preset->basePath());
        $this->assertSame("{$workingPath}/app", $preset->sourcePath());
        $this->assertSame("{$workingPath}/resources", $preset->resourcePath());
        $this->assertSame("{$workingPath}/resources/views", $preset->viewPath());
        $this->assertSame("{$workingPath}/database/factories", $preset->factoryPath());
        $this->assertSame("{$workingPath}/database/migrations", $preset->migrationPath());
        $this->assertSame("{$workingPath}/database/seeders", $preset->seederPath());

        $this->assertSame('Workbench\App\\', $preset->rootNamespace());
        $this->assertSame('Workbench\App\Console\\', $preset->commandNamespace());
        $this->assertSame('Workbench\App\Models\\', $preset->modelNamespace());
        $this->assertSame('Workbench\App\Providers\\', $preset->providerNamespace());
        $this->assertSame('Workbench\Database\Factories\\', $preset->factoryNamespace());
        $this->assertSame('Workbench\Database\Seeders\\', $preset->seederNamespace());
        // $this->assertSame('Tests\\', $preset->testingNamespace());

        $this->assertFalse($preset->hasCustomStubPath());
        $this->assertSame('Illuminate\Foundation\Auth\User', $preset->userProviderModel());
    }

    /** @test */
    public function it_cant_access_testing_path()
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Generating test is not supported for [workbench] preset');

        $this->app[PresetManager::class]->driver('workbench')->testingPath();
    }

    /** @test */
    public function it_cant_access_testing_namespace()
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Generating test is not supported for [workbench] preset');

        $this->app[PresetManager::class]->driver('workbench')->testingNamespace();
    }
}
