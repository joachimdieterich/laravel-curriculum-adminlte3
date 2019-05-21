<?php

/*
 * To change this license header, choose License Headers in Organization Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OrganizationFactory.
 *
 * @author joachimdieterich
 */

namespace Tests\Setup;

use App\Organization;
use App\User;

class UserFactory
{
   

    public function create()
    {
        $user = factory(User::class)->create();

        return $user;
    }
}
