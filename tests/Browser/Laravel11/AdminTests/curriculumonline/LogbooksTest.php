<?php

namespace Tests\Browser;

use App\Period;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LogbooksTest extends DuskTestCase
{
    /**
     * Testing Curriculum Logbook from Admin
     *
     * @return void
     */
    public function testLogbookExample(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))    
                    ->visit('/logbooks')
                    ->waitForText('Create Logbook')
                    ->click('#app > div.content-wrapper.d-flex.flex-column > section.content.d-flex.flex-column.flex-fill > div > div:nth-child(2) > div')
                    ->type('#title','Test')
                    //TinyMCE Damagend, Description needs to be done after fix
                    ->press('#logbook-save')
                    // !REMOVING THIS PAUSE CAUSES A RUNTIME BUG!
                    ->pause(3000)
                    ->click('#app > div.content-wrapper.d-flex.flex-column > section.content.d-flex.flex-column.flex-fill > div > div:nth-child(2) .box:last-child')
                    ->waitForText('Create Logbookentry')
                    ->press('Create Logbookentry')
                    ->type('title','Test')
                    ->press('Save')
                    //Save funktion does not work, Bug Report, Project on Pause
                    ->pause(1000)

                    //Selfdeletion
                    ->visit('/logbooks')
                    ->waitForText('Create Logbook')
                    ->pause(1500)
                    ->click('#app > div.content-wrapper.d-flex.flex-column > section.content.d-flex.flex-column.flex-fill > div > div:nth-child(2) .box:last-child .btn')
                    ->waitForText('Delete Logbook')
                    ->press('Delete Logbook')
                   
                    ;
        });
    }
}