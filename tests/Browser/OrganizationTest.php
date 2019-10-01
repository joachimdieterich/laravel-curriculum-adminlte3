<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Chrome;
use App\User;
use App\Organization;


class OrganizationTest extends DuskTestCase
{
    
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testGetOrganization()
    {   
        $this->browse(function (Browser $browser) {
            $organization = Organization::first();
            $browser->visit('/')
                    ->type('email', 'admin@curriculumonline.de')
                    ->type('password', 'password') // Enter plain password
                    ->press('Login')
                    ->visit(new Pages\OrganizationPage)
                    ->waitForText($organization->title)
                    ->assertSee('Add Organization')
                    ->click('#add-organization')
                    
                    ->type('title', 'DuskTest')
                    ->type('description', 'DuskTest Description')
                    ->type('street', 'Dusk Street')
                    ->type('postcode', '12345')
                    ->type('city', 'Dusk City')
                    ->type('phone', '0123 456789')
                    ->type('email', 'dusk@curriculumonline.de')
                    ->select('status', 'aktiv')
                    
                    ->click('#organization-save')
                    ->screenshot('add-organization');
        });
        
        $organization = Organization::where('title', 'DuskTest')->get();
        $this->assertDatabaseHas('organizations', $organization->toArray());
    }
}
