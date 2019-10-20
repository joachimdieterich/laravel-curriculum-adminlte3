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

use App\Certificate;

class CertificateFactory
{
    protected $user;

    public function ownedBy($user)
    {
        $this->user = $user;

        return $this;
    }

    public function create()
    {
        
        return  factory(Certificate::class)->create();
    }
}
