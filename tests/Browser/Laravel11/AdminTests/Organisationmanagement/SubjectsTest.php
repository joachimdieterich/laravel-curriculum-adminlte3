<?php

namespace Tests\Browser;

use App\Period;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SubjectsTest extends DuskTestCase
{
    /**
     * Testing Curriculum Subjects from Admin
     *
     * @return void
     */
    public function testSubjectsExample(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))   
                    ->visit('/subjects')
                    
                    ;
        });
    }
}