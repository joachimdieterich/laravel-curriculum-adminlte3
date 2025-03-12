<?php

namespace Tests\Browser;

use App\Period;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PermissionsTest extends DuskTestCase
{
    /**
     * Testing Curriculum Permissions from Admin
     *
     * @return void
     */
    public function testPermissionsExample(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))   
                    ->visit('/permissions')
                    
                    ;
        });
    }
}