<?php

namespace App\Plugins\Eventmanagement;

/**
 * @author joachimdieterich
 */
class EventmanagementPlugin
{
    public $plugins = [];

    public function __construct()
    {
        $plugin = env('EVENTMANAGEMENTPLUGIN', null);
        if ($plugin != null) {
            $class = '\\App\\Plugins\\Eventmanagement\\'.$plugin.'\\'.$plugin;
            $this->plugins[$plugin] = new $class();
        }
    }
}
