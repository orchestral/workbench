<?php

namespace Orchestra\Workbench;

use Illuminate\Support\Manager;

class RecipeManager extends Manager
{
    public function createAssetPublishDriver(): Contracts\Recipe
    {
        return new Recipes\AssetPublish();
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
