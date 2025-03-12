<?php

namespace Tests\Browser;

use App\Period;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UseresTest extends DuskTestCase
{
    /**
     * Testing Curriculum Users from Admin
     *
     * @return void
     */
    public function testUsersExample(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))  
                    ->visit('/users')
                    ->waitForText('Create user')
                    ->click('#user-content > div:nth-child(2)')
                    ->type('username','Test')
                    ->type('firstname','Testus')
                    ->type('lastname','Maximus')
                    ->type('email','Test@gmail.com')
                    ->pause(4000)
                    //Test not working on Password, couldnt figure out why
                    ->type('password','test')
                    ->pause(4000)
                    ->press('Save')
                    ->pause(3000)
                    //Test not working, major Bug (Users get deleted on trying to create new one)
                    ;
        });
    }
}