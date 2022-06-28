<?php

namespace App\Plugins\Lms;

/**
 * @author joachimdieterich
 */
class LmsPlugin
{
    public $plugins = [];

    public function __construct()
    {
        $plugin = env('LMSPLUGIN', null);
        if ($plugin != null) {
            $class = '\\App\\Plugins\\Lms\\'.$plugin.'\\'.$plugin;
            $this->plugins[$plugin] = new $class();
        }
    }
}
