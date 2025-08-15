<?php

namespace Tests\Browser;

use App\Period;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class GroupTest extends DuskTestCase
{
    /**
     * Testing Curriculum Group from Admin
     *
     * @return void
     */
    public function testGroupExample(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))   
                    ->visit('/groups')
                    ->waitForText('Create Group')
                    ->click('#group-content > div:nth-child(2)')
                    ->type('#title','Test')
                    //->click('#select2-grade_id-container')
                    ->pause(4000)
                    ->select2('#grade_id', '2. Klasse')
                    ->select2('#period_id', 'Test')
                    ->select2('#organization_id', 'curriculumonline')
                    //->click('#select2-grade_id-results .box:first-child')
                    //->press('1.Klasse')
                    //->select('#grade_id','1. Klasse')
                    //->press('Save')   
                    ->pause(5000)                 
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