<?php

/*
 * To change this license header, choose License Headers in Organization Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EnablingObjectiveFactory.
 *
 * @author joachimdieterich
 */

namespace Tests\Setup;

use App\EnablingObjective;

class EnablingObjectiveFactory
{
    public function create()
    {
        return factory(EnablingObjective::class)->create();
    }
}
