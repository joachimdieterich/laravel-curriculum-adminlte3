<?php
namespace App;

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
            $class = '\\App\\'.$plugin;
        $this->plugins[$plugin] = new $class();
        }
        
    }
    
}

