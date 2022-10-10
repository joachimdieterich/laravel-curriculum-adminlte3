<?php

namespace App\Plugins\Repositories;

/**
 * @author joachimdieterich
 */
class RepositoryPlugin
{
    public $plugins = [];

    public function __construct()
    {
        $this->plugins['edusharing'] = new \App\Plugins\Repositories\edusharing\Edusharing();
    }
}
