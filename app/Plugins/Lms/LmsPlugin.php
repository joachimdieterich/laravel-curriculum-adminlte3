<?php

namespace App\Plugins\Lms;

/**
 *
 * @author joachimdieterich
 */
class LmsPlugin
{
    public $plugins = array();

    public function __construct()
    {
        $plugin = env('LMSPLUGIN', NULL);
        if ($plugin != NULL) {
            $class = '\\App\\Plugins\\Lms\\' . $plugin . '\\' . $plugin;
            $this->plugins[$plugin] = new $class();
        }

    }

}

