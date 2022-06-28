<?php

/**
 * Description of PermissionFactory.
 *
 * @author joachimdieterich
 */

namespace Tests\Setup;

use App\Permission;

class PermissionFactory
{
    protected $user;

    public function create()
    {
        $role = factory(Permission::class)->create();

        return $role;
    }
}
