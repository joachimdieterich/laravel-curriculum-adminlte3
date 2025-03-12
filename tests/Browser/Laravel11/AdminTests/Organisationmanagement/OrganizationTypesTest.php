<?php

namespace Tests\Browser;

use App\Period;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class OrganizationTypesTest extends DuskTestCase
{
    /**
     * Testing Curriculum Curricula from Admin
     *
     * @return void
     */
    public function testOrganizationTypesExample(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))   
                    ->visit('/organizationTypes')
                    
                    ;
        });
    }
}