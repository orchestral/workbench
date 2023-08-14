<?php

namespace Orchestra\Workbench\Contracts;

interface RecipeManager
{
    /**
     * Create anonymous command driver.
     *
     * @return \Orchestra\Workbench\Contracts\Recipe
     */
    public function commandUsing(string $command): Recipe;

    /**
     * Run the recipe by name.
     *
     * @param  string  $driver
     * @return \Orchestra\Workbench\Contracts\Recipe
     */
    public function command(string $driver): Recipe;

    /**
     * Determine recipe is available by name.
     *
     * @param  string  $driver
     * @return bool
     */
    public function hasCommand(string $driver): bool;
}
