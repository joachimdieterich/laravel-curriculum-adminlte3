<?php
namespace App;

/**
 *
 * @author joachimdieterich
 */
class RepositoryPlugin {
    public $plugins = array();
    
    public function __construct()
    {
        $this->plugins['edusharing'] = new Edusharing();
    }
    
}

