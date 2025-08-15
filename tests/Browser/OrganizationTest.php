<?php

namespace Tests\Browser;

use App\Organization;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class OrganizationTest extends DuskTestCase
{
    /**
     * Add organization
     *
     * @return void
     */
    public function testAddOrganization()
    {
        $this->browse(function (Browser $browser) {
            $organization = Organization::first();
            $browser
                ->visit('/login')
                ->type('email', 'admin@curriculumonline.de')
                ->type('password', 'password') // Enter plain password
                ->press('login')
                ->visit(new Pages\OrganizationPage)
                ->waitForText($organization->title)
                ->assertSee(trans('global.organization.create'))
                ->click('#add-organization')

                ->type('title', 'DuskTest');
            $browser
                ->driver->executeScript('tinyMCE.get(\'description\').setContent(\'<p>DuskTest Description</p>\')');
            $browser
                ->type('street', 'Dusk Street')
                ->type('postcode', '12345')
                ->type('city', 'Dusk City')
                ->type('phone', '0123 456789')
                ->type('email', 'dusk@curriculumonline.de')
                ->select2('#status_definition_id', 'aktiv')
                ->screenshot('/admin/organization-add')

                ->press('#organization-save')
                ->waitForText('DuskTest');
        });
    }

    /**
     * Edit organization
     *
     * @return void
     */
    public function testShowOrganization()
    {
        $this->browse(function (Browser $admin) {
            $organization = Organization::create([
                'title' => 'DuskTest',
                'description' => 'DuskTest Description',
                'street' => 'Dusk Street',
                'postcode' => '12345',
                'city' => 'Dusk City',
                'phone' => '0123 456789',
                'email' => 'dusk@curriculumonline.de',
                'status_id' => 1,
            ]);
            $admin->loginAs(User::find(1))
                    ->visit(new Pages\OrganizationPage)
                    ->waitForText('DuskTest')
                    ->click("#show-organization-{$organization->id}")
                    ->waitForText($organization->title)
                    ->assertSee($organization->description)
                    ->assertSee($organization->street)
                    ->assertSee($organization->postcode)
                    ->assertSee($organization->city)
                    ->assertSee($organization->phone)
                    ->assertSee($organization->email)
                    ->screenshot('/admin/organization-show');
        });
    }

    /**
     * Edit organization
     *
     * @return void
     */
    public function testEditOrganization()
    {
        $this->browse(function (Browser $admin) {
            $organization = Organization::create([
                'title' => 'DuskTest',
                'description' => 'DuskTest Description',
                'street' => 'Dusk Street',
                'postcode' => '12345',
                'city' => 'Dusk City',
                'phone' => '0123 456789',
                'email' => 'dusk@curriculumonline.de',
                'status_id' => 1,
            ]);
            $admin->loginAs(User::find(1))
                    ->visit(new Pages\OrganizationPage)
                    ->waitForText('DuskTest')
                    ->click('#edit-organization-'.$organization->id)
                    ->waitForText('DuskTest')
                    ->type('title', 'DuskTest changed')
                    ->click('#organization-save')
                    ->waitForText('DuskTest')
                    ->screenshot('/admin/organization-edit');
        });
    }

    /**
     * Delete organization
     *
     * @return void
     */
    public function testDeleteOrganization()
    {
        $this->browse(function (Browser $admin) {
            $organization = Organization::create([
                'title' => 'DuskTest',
                'description' => 'DuskTest Description',
                'street' => 'Dusk Street',
                'postcode' => '12345',
                'city' => 'Dusk City',
                'phone' => '0123 456789',
                'email' => 'dusk@curriculumonline.de',
                'status_id' => 1,
            ]);
            $admin->loginAs(User::find(1))
                    ->visit(new Pages\OrganizationPage)
                    ->waitForText('DuskTest')
                    ->click('#delete-organization-'.$organization->id)
                    ->waitForText('curriculumonline') //proof that datatable is loaded, curriculumonline is the seeded demo organization
                    ->assertDontSee('DuskTest')
                    //->screenshot('/admin/see-organization')
;
        });
    }
}
