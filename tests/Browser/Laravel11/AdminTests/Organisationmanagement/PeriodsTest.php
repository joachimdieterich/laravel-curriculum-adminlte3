<?php

namespace Tests\Browser;

use App\Period;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PeriodsTest extends DuskTestCase
{
    /**
     * Testing Curriculum Periods from Admin
     *
     * @return void
     */
    public function testPeriodsExample(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))   
                    ->visit('/periods')
                    
                    ;
        });
    }
}