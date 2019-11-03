<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Chrome;
use App\User;


class ExampleTest extends DuskTestCase
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
