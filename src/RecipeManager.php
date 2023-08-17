<?php

namespace Orchestra\Workbench;

use Illuminate\Support\Manager;
use Illuminate\Support\Str;

class RecipeManager extends Manager implements Contracts\RecipeManager
{
    /**
     * Create "asset-publish" driver.
     *
     * @return \Orchestra\Workbench\Contracts\Recipe
     */
    public function createAssetPublishDriver(): Contracts\Recipe
    {
        return new Recipes\AssetPublishCommand();
    }

    /**
     * Create "create-sqlite-db" driver.
     *
     * @return \Orchestra\Workbench\Contracts\Recipe
     */
    public function createCreateSqliteDbDriver(): Contracts\Recipe
    {
        return new Recipes\Command('workbench:create-sqlite-db');
    }

    /**
     * Create "drop-sqlite-db" driver.
     *
     * @return \Orchestra\Workbench\Contracts\Recipe
     */
    public function createDropSqliteDbDriver(): Contracts\Recipe
    {
        return new Recipes\Command('workbench:drop-sqlite-db');
    }

    /**
     * Create anonymous command driver.
     *
     * @return \Orchestra\Workbench\Contracts\Recipe
     */
    public function commandUsing(string $command): Contracts\Recipe
    {
        return new Recipes\Command($command);
    }

    /**
     * Run the recipe by name.
     *
     * @return \Orchestra\Workbench\Contracts\Recipe
     */
    public function command(string $driver): Contracts\Recipe
    {
        return $this->driver($driver);
    }

    /**
     * Determine recipe is available by name.
     */
    public function hasCommand(string $driver): bool
    {
        if (isset($this->customCreators[$driver])) {
            return true;
        }

        $method = 'create'.Str::studly($driver).'Driver';

        return method_exists($this, $method);
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return 'asset-publish';
    }
}
