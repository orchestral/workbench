<?php

namespace Orchestra\Workbench;

use Illuminate\Support\Collection;
use Illuminate\Support\Manager;

class RecipeManager extends Manager
{
    /**
     * Create "asset-publish" driver.
     *
     * @return \Orchestra\Workbench\Contracts\Recipe
     */
    public function createAssetPublishDriver(): Contracts\Recipe
    {
        /** @var array<int, string> $assets */
        $assets = Workbench::config('assets');

        $tags = Collection::make($assets)
            ->push('laravel-assets')
            ->unique()
            ->all();

        return new Recipes\Command('vendor:publish', [
            '--tag' => $tags,
            '--force' => true,
        ]);
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
     * Create "db-wipe" driver.
     *
     * @return \Orchestra\Workbench\Contracts\Recipe
     */
    public function createDbWipeDriver(): Contracts\Recipe
    {
        return new Recipes\Command('db:wipe');
    }

    /**
     * Create "migrate" driver.
     *
     * @return \Orchestra\Workbench\Contracts\Recipe
     */
    public function createMigrateDriver(): Contracts\Recipe
    {
        return new Recipes\Command('migrate');
    }

    /**
     * Create "migrate-fresh" driver.
     *
     * @return \Orchestra\Workbench\Contracts\Recipe
     */
    public function createMigrateFreshDriver(): Contracts\Recipe
    {
        return new Recipes\Command('migrate:fresh');
    }

    /**
     * Create "migrate-refresh" driver.
     *
     * @return \Orchestra\Workbench\Contracts\Recipe
     */
    public function createMigrateRefreshDriver(): Contracts\Recipe
    {
        return new Recipes\Command('migrate:refresh');
    }

    /**
     * Run the action by name.
     *
     * @param  string  $name
     * @return \Orchestra\Workbench\Contracts\Recipe
     */
    public function action(string $name): Contracts\Recipe
    {
        return $this->driver($name);
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
