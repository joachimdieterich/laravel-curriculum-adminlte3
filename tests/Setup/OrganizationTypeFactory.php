<?php

/*
 * To change this license header, choose License Headers in Organization Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OrganizationTypeFactory.
 *
 * @author joachimdieterich
 */

namespace Tests\Setup;

use App\OrganizationType;

class OrganizationTypeFactory
{
    public function create()
    {
        return  factory(OrganizationType::class)->create();
    }
}
