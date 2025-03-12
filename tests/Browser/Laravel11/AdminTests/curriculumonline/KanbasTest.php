<?php

namespace Tests\Browser;

use App\Period;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class KanbasTest extends DuskTestCase
{
    /**
     * Testing Curruculum Kanban from Admin
     *
     * @return void
     */
    public function testKanbansExample(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))    
                    ->visit('/kanbans')
                    ->waitForText('Create Kanban')
                    ->click('#kanban-content > div')
                    ->type('title','Test')
                    ->type('description','Test')
                    //->click('body > div.modal-mask > div > div.card-body > div:nth-child(6) > span:nth-child(2)')
                    ->press('#kanban-save')
                    // !REMOVING THIS PAUSE CAUSES A RUNTIME BUG!
                    ->pause(3000)
                    ->click('#kanban-content .box:last-child')
                    ->pause(3000)
                    ->click('#kanbanStatusCreate_0 > strong')
                   /*  ->type('title_0','Test 1')
                    ->press('Save') */

                    // Webside Breaking Bugs found, Dokumentent in Bug Report
                  

                    //Selfdeletion
                    ->visit('/kanbans')
                    ->waitForText('Create Kanban')
                    ->pause(1500)
                    ->click('#kanban-content .box:last-child .btn')
                    ->waitForText('Delete Kanban')
                    ->press('Delete Kanban') 
                   
                    ;
        });
    }
}