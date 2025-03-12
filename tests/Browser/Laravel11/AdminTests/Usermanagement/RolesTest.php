<?php

namespace Tests\Browser;

use App\Period;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RolesTest extends DuskTestCase
{
    /**
     * Testing Curriculum Roles from Admin
     *
     * @return void
     */
    public function testRolesExample(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))   
                    ->visit('/roles')
                    
                    ;
        });
    }
}