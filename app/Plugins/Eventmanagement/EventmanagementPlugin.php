<?php
namespace App\Plugins\Eventmanagement;

/**
 *
 * @author joachimdieterich
 */
class EventmanagementPlugin {
    public $plugins = array();

    public function __construct()
    {
        $plugin = env('EVENTMANAGEMENTPLUGIN', NULL);
        if ($plugin != NULL)
        {
            $class = '\\App\\Plugins\\Eventmanagement\\'.$plugin.'\\'.$plugin;
        $this->plugins[$plugin] = new $class();
        }

    }

}

