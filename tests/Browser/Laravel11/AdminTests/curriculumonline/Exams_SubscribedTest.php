<?php

namespace Tests\Browser;

use App\Period;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class Exams_SubscribedTest extends DuskTestCase
{
    /**
     * Testing Curriculum Learning Analysis from Admin
     *
     * @return void
     */
    public function testExams_SubscribedExample(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))   
                    ->visit('/exams_subscribed')
                    
                    ;
        });
    }
}