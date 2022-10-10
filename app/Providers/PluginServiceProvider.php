<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Plugins\Repositories\RepositoryPlugin;

class PluginServiceProvider extends ServiceProvider
{
    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        RepositoryPlugin::class => RepositoryPlugin::class,
    ];
}
