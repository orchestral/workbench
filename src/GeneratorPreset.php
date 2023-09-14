<?php

namespace Orchestra\Workbench;

use BadMethodCallException;
use Illuminate\Console\Generators\Presets\Laravel;
use Illuminate\Contracts\Config\Repository as ConfigContract;

class GeneratorPreset extends Laravel
{
    /**
     * Construct a new preset.
     *
     * @return void
     */
    public function __construct(ConfigContract $config)
    {
        parent::__construct('Workbench\\', rtrim(Workbench::path(), DIRECTORY_SEPARATOR), $config);
    }

    /**
     * Preset name.
     *
     * @return string
     */
    public function name()
    {
        return 'workbench';
    }

    /**
     * Preset has custom stub path.
     *
     * @return bool
     */
    public function hasCustomStubPath()
    {
        return false;
    }

    /**
     * Get the path to the base working directory.
     *
     * @return string
     */
    public function laravelPath()
    {
        return app()->basePath();
    }

    /**
     * Preset namespace.
     *
     * @return string
     */
    public function rootNamespace()
    {
        return "{$this->rootNamespace}App\\";
    }

    /**
     * Model namespace.
     *
     * @return string
     */
    public function modelNamespace()
    {
        return "{$this->rootNamespace()}Models\\";
    }

    /**
     * Database factory namespace.
     *
     * @return string
     */
    public function factoryNamespace()
    {
        return "{$this->rootNamespace}Database\Factories\\";
    }

    /**
     * Database seeder namespace.
     *
     * @return string
     */
    public function seederNamespace()
    {
        return "{$this->rootNamespace}Database\Seeders\\";
    }

    /**
     * Testing namespace.
     *
     * @throws \BadMethodCallException
     */
    public function testingNamespace()
    {
        throw new BadMethodCallException('Generating test is not supported for [workbench] preset');
    }
}
