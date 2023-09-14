<?php

namespace Orchestra\Workbench;

use BadMethodCallException;
use Illuminate\Console\Generators\Presets\Laravel;

class GeneratorPreset extends Laravel
{
    /**
     * Construct a new preset.
     *
     * @param  \Illuminate\Contracts\Config\Repository  $config
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
     * Model namespace.
     *
     * @return string
     */
    public function modelNamespace()
    {
        return "{$this->rootNamespace()}Models\\";
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
