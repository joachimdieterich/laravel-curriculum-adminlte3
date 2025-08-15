<?php

namespace Tests\Browser;

use App\Navigator;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class NavigatorTest extends DuskTestCase
{
    /**
     * Add navigator
     *
     * @return void
     */
    public function testAddNavigator()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->type('email', 'admin@curriculumonline.de')
                    ->type('password', 'password') // Enter plain password
                    ->press('Login')
                    ->visit(new Pages\NavigatorPage)
                    ->waitForText('Add Navigator')
                    ->click('#add-navigator')
                    ->type('title', 'DuskNavigator')
                    ->select2('#organization_id', 'curriculumonline')

                    ->click('#navigator-save')
                    ->waitForText('DuskNavigator');
        });
    }

    /**
     * Edit navigator
     *
     * @return void
     */
    public function testShowNavigator()
    {
        $this->browse(function (Browser $admin) {
            $navigator = Navigator::create([
                'title' => 'DuskNavigator',
                'organization_id' => 1,
            ]);
            $admin->loginAs(User::find(1))
                    ->visit(new Pages\NavigatorPage)
                    ->waitForText($navigator->title)
                    ->click('#show-navigator-'.$navigator->id)
                    ->waitForText($navigator->title)
                    //->screenshot('see-navigatoral-details')
;
        });
    }

    /**
     * Edit navigator
     *
     * @return void
     */
    public function testEditNavigator()
    {
        $this->browse(function (Browser $admin) {
            $navigator = Navigator::create([
                'title' => 'DuskNavigator',
                'organization_id' => 1,
            ]);
            $admin->loginAs(User::find(1))
                    ->visit(new Pages\NavigatorPage)
                    ->waitForText($navigator->title)
                    ->click('#edit-navigator-'.$navigator->id)
                    ->type('title', 'DuskNavigator changed')
                    ->click('#navigator-save')
                    ->waitForText('DuskNavigator changed')
                    //->screenshot('see-navigator')
;
        });
    }

    /**
     * Delete navigator
     *
     * @return void
     */
    public function testDeleteNavigator()
    {
        $this->browse(function (Browser $admin) {
            $navigator = Navigator::create([
                'title' => 'DuskNavigator',
                'organization_id' => 1,
            ]);
            $navigator = Navigator::create([
                'title' => 'DuskNavigator2',
                'organization_id' => 1,
            ]);
            $admin->loginAs(User::find(1))
                    ->visit(new Pages\NavigatorPage)
                    ->waitForText($navigator->title)
                    ->click('#delete-navigator-'.$navigator->id)
                    ->waitForText('DuskNavigator') //proof that datatable is loaded, curriculumonline is the seeded demo navigator
                    ->assertDontSee('DuskNavigator2')
                    //->screenshot('see-navigator')
;
        });
    }
}
