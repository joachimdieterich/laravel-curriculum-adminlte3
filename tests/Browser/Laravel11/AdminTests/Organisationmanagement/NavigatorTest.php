<?php

namespace Tests\Browser;

use App\Period;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class NavigatorTest extends DuskTestCase
{
    /**
     * Testing Curriculum Navigators from Admin
     *
     * @return void
     */
    public function testNavigatorExample(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))    
                    ->visit('/navigators')
                    ->waitForText('Create Navigator')
                    ->click('#navigator-content > div:nth-child(1)')
                    ->type('#title','Test')
                    ->select('#organization_id_form_group > span > span.selection > span','2')
                    ->press('Save')
                    // Navigators cant be opend or deleted

                    //Selfdeletion
                    /* ->visit('/plans')
                    ->waitForText('Create Logbook')
                    ->pause(1500)
                    ->click('#plan-content > div .box:last-child .btn')
                    ->waitForText('Delete Logbook')
                    ->press('Delete Logbook') */
                   
                    ;
        });
    }
}