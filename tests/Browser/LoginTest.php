<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testOrganization()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->type('email', 'admin@curriculumonline.de')
                    ->type('password', 'password') // Enter plain password
                    ->press('Login')
                    ->visit(new Pages\OrganizationPage)
                    ->assertSee('Add Organization');
        });
    }
}
