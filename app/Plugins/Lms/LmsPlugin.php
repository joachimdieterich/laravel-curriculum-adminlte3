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
        $plugin = config('app.lms_plugin');
        if ($plugin != null) {
            $class = '\\App\\Plugins\\Lms\\'.$plugin.'\\'.$plugin;
            $this->plugins[$plugin] = new $class();
        }
    }
}
