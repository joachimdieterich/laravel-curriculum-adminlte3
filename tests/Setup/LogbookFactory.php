<?php

/**
 * Description of LogbookFactory.
 *
 * @author joachimdieterich
 */

namespace Tests\Setup;

use App\Logbook;

class LogbookFactory
{
    protected $user;

    public function ownedBy($user)
    {
        $this->user = $user;

        return $this;
    }

    public function create()
    {

        return factory(Logbook::class)->create();
    }
}
