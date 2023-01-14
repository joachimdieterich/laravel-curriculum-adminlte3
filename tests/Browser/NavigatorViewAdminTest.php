<?php

namespace Tests\Browser;

use App\Navigator;
use App\NavigatorView;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class NavigatorViewAdminTest extends DuskTestCase
{
    /**
     * Add navigator
     *
     * @return void
     */
    public function testAddNavigatorView()
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
                    ->click('#add-navigator-view')

                    ->type('title', 'First View')
                    ->type('description', 'First View description')
                    //->screenshot('see-navigator-add-view')
                    ->click('#navigator-view-save');
        });
    }

    /**
     * Edit navigator
     *
     * @return void
     */
    public function testShowNavigatorView()
    {
        $this->browse(function (Browser $admin) {
            $navigator = Navigator::create([
                'title' => 'DuskNavigator',
                'organization_id' => 1,
            ]);
            $navigatorView = NavigatorView::create([
                'title' => 'First View',
                'description' => 'First View Description',
                'navigator_id' => $navigator->id,
            ]);

            $admin->loginAs(User::find(1))
                ->visit('/navigators/'.$navigator->id.'/'.$navigatorView->id)
                ->waitForText($navigatorView->title)
                ->assertSee($navigatorView->description)
                //->screenshot('see-navigatoral-details')
;
        });
    }

    /**
     * Edit navigator
     *
     * @return void
     */
    public function testEditNavigatorView()
    {
        $this->browse(function (Browser $admin) {
            $navigator = Navigator::create([
                'title' => 'DuskNavigator',
                'organization_id' => 1,
            ]);
            $navigatorView = NavigatorView::create([
                'title' => 'First View',
                'description' => 'First View Description',
                'navigator_id' => $navigator->id,
            ]);

            $admin->loginAs(User::find(1))
                ->visit('/navigators/'.$navigator->id.'/'.$navigatorView->id)
                ->waitForText($navigatorView->title)
                ->assertSee($navigatorView->description)
                ->click('#edit-navigator-view')
                //->screenshot('see-nav-view-edit')
                ->waitForText($navigatorView->title)
                ->type('title', $navigatorView->title.' changed')
                ->type('description', $navigatorView->description.' changed')
                ->click('#navigator-view-save')
                ->waitForText($navigatorView->title.' changed')
                ->assertSee($navigatorView->description.' changed');
        });
    }

    /**
     * Delete navigator
     *
     * @return void
     */
    public function testDeleteNavigatorView()
    {
        $this->browse(function (Browser $admin) {
            $navigator = Navigator::create([
                'title' => 'DuskNavigator',
                'organization_id' => 1,
            ]);
            $navigatorView = NavigatorView::create([
                'title' => 'First View',
                'description' => 'First View Description',
                'navigator_id' => $navigator->id,
            ]);

            $admin->loginAs(User::find(1))
                ->visit('/navigators/'.$navigator->id.'/'.$navigatorView->id)
                ->waitForText($navigatorView->title)
                ->assertSee($navigatorView->description)
                ->click('#delete-navigator-view')
                ->waitForText($navigator->title)
                ->assertDontSee($navigatorView->title)
                ->assertDontSee($navigatorView->description);
        });
    }
}
