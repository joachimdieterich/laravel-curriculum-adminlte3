<?php

/*
 * To change this license header, choose License Headers in Role Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RoleFactory.
 *
 * @author joachimdieterich
 */

namespace Tests\Setup;

use App\Role;

class RoleFactory
{
    protected $user;

    public function create()
    {
        $role = factory(Role::class)->create();

        return $role;
    }
}
