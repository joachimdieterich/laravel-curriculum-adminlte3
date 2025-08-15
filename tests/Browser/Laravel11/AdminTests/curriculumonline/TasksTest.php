<?php

namespace Tests\Browser;

use App\Period;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TasksTest extends DuskTestCase
{
    /**
     * Testing Curriculum Tasks from Admin
     *
     * @return void
     */
    public function testTasksExample(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))    
                    ->visit('/plans')
                    ->waitForText('Create plan')
                    ->click('#plan-content > div')
                    ->type('#title','Test')
                    ->type('external_begin','1.01.2001')
                    ->type('external_end','30.12.2001')
                    ->press('Save')
                    // Save Button doenst work, Infos in Bug Report

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