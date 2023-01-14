<?php

namespace Tests\Browser;

use App\Curriculum;
use App\Navigator;
use App\NavigatorItem;
use App\NavigatorView;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class NavigatorItemTest extends DuskTestCase
{
    /**
     * Add navigator view as navigator item
     * - show new view
     * - delete new view
     *
     * @return void
     */
    public function testAddNavigatorViewAsNavigatorItemAndShowNewViewThenDeleteView()
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
            $title = 'Second View';
            $description = 'Second View description';
            $admin->loginAs(User::find(1))
                    ->visit('navigators/'.$navigator->id) //shows first view of navigator.
                    ->waitForText($navigator->title)
                    ->assertSee($navigatorView->title)
                    ->click('#add-navigator-items')
                    ->waitForText(trans('global.create').' '.trans('global.navigator_item.title_singular'))
                    ->select2('#referenceable_type', trans('global.referenceable_types.navigator_view'))
                    ->type('title', $title)
                    ->type('description', $description)
                    ->select2('#medium_id', 1)
                    ->select2('#position', trans('global.content'))
                    ->select2('#css_class', 'col-xs-12')
                    ->select2('#visibility', trans('global.navigator_item.fields.visibility_show'))
                    ->click('#navigator-item-save')
                    ->waitForText($title)
                    ->assertSee($description);
            //Show new navigator view
            $navigator_item = NavigatorItem::where('title', $title)->first();
            $admin->click('#navigator-item-'.$navigator_item->id)
                    ->waitForText($navigator_item->title)
                    ->assertSee($navigator_item->description)
                    //Delete navigator view
                    ->click('#delete-navigator-view')
                    ->waitForText($navigator->title)
                    ->assertDontSee($title)
                    ->assertDontSee($description);
        });
    }

    /**
     * Add curriculum as navigator item
     *
     * @return void
     */
    public function testAddCurriculumAsNavigatorItem()
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
            $curriculum = Curriculum::find(1);
            $admin->loginAs(User::find(1))
                    ->visit('navigators/'.$navigator->id) //shows first view of navigator.
                    ->waitForText($navigator->title)
                    ->assertSee($navigatorView->title)
                    ->click('#add-navigator-items')
                    ->waitForText(trans('global.create').' '.trans('global.navigator_item.title_singular'))
                    ->select2('#referenceable_type', trans('global.referenceable_types.curriculum'))
                    ->select2('#referenceable_id', $curriculum->title)
                    ->select2('#position', trans('global.content'))
                    ->select2('#css_class', 'col-xs-12')
                    ->select2('#visibility', trans('global.navigator_item.fields.visibility_show'))
                    ->click('#navigator-item-save')
                    ->waitForText($curriculum->title)
                    ->assertSee($curriculum->description);
            //Show new navigator item
            $navigator_item = NavigatorItem::where('title', $curriculum->title)->first();
            $admin->click('#navigator-item-'.$navigator_item->id)
                    ->waitForText($curriculum->title)
                    ->visit('navigators/'.$navigator->id) //shows first view of navigator.
                    ->waitForText($curriculum->title)
                    //Delete navigator item
                    ->click('#delete-navigator-item-'.$navigator_item->id)
                    ->waitForText($navigator->title)
                    ->assertDontSee($curriculum->title);
        });
    }

    /**
     * Add text as navigator item
     *
     * @return void
     */
    public function testAddTextAsNavigatorItem()
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
            $title = 'Navigator text title';
            $description = 'Navigator text description';
            $admin->loginAs(User::find(1))
                    ->visit('navigators/'.$navigator->id) //shows first view of navigator.
                    ->waitForText($navigator->title)
                    ->assertSee($navigatorView->title)
                    ->click('#add-navigator-items')
                    ->waitForText(trans('global.create').' '.trans('global.navigator_item.title_singular'))
                    ->select2('#referenceable_type', trans('global.referenceable_types.content'))
                    ->type('title', $title)
                    ->type('description', $description)
                    ->select2('#position', trans('global.header'))
                    ->select2('#css_class', 'col-12')
                    ->select2('#visibility', trans('global.navigator_item.fields.visibility_show'))
                    ->click('#navigator-item-save')
                    ->waitForText($title)
                    ->click('.box-title') //expand item
                    ->assertSee($description);
            //Show new navigator item
            $navigator_item = NavigatorItem::where('title', $title)->first();
            //Delete navigator item
            $admin->click('#delete-navigator-content-'.$navigator_item->id)
                    ->waitForText($navigator->title)
                    ->assertDontSee($title);
        });
    }

    /**
     * Add medium as navigator item
     *
     * @return void
     */
    public function testAddMediumAsNavigatorItem()
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
            $title = 'Medium title';
            $description = 'Medium description';
            $admin->loginAs(User::find(1))
                    ->visit('navigators/'.$navigator->id) //shows first view of navigator.
                    ->waitForText($navigator->title)
                    ->assertSee($navigatorView->title)
                    ->click('#add-navigator-items')
                    ->waitForText(trans('global.create').' '.trans('global.navigator_item.title_singular'))
                    ->select2('#referenceable_type', trans('global.referenceable_types.medium'))
                    ->type('title', $title)
                    ->type('description', $description)
                    ->select2('#medium_id', 1)
                    ->select2('#position', trans('global.content'))
                    ->select2('#css_class', 'col-xs-12')
                    ->select2('#visibility', trans('global.navigator_item.fields.visibility_show'))
                    ->click('#navigator-item-save')
                    ->waitForText($title)
                    ->assertSee($description);
            //Show new navigator item
            $navigator_item = NavigatorItem::where('title', $title)->first();

            //Delete navigator item
            $admin->click('#delete-navigator-item-'.$navigator_item->id)
                    ->waitForText($navigator->title)
                    ->assertDontSee($title);
        });
    }
}
