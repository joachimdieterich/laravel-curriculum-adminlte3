<?php

namespace Tests\Browser\UseCases\Guest;

use App\Curriculum;
use App\Navigator;
use App\NavigatorItem;
use App\NavigatorView;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class GuestNavigatorTest extends DuskTestCase
{
    use DatabaseMigrations;

    public $navigator;

    public $navigator_view;

    public $navigator_item;

    /**
     * Setup Navigator, add view and all available items
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->navigator = Navigator::create([
            'title' => 'DuskNavigator',
            'organization_id' => 1,
        ]);
        $this->navigatorView = NavigatorView::create([
            'title' => 'First View',
            'description' => 'First View Description',
            'navigator_id' => $this->navigator->id,
        ]);
        $curriculum = Curriculum::find(1);
        $this->navigator_item['view'] = ['title' => 'Second View', 'description' => 'Second View description'];
        $this->navigator_item['curriculum'] = ['title' => $curriculum->title];
        $this->navigator_item['content'] = ['title' => 'Navigator text title', 'description' => 'Navigator text description'];
        $this->navigator_item['medium'] = ['title' => 'Medium medium', 'description' => 'Medium medium description'];

        // generate item view
        $this->browse(function (Browser $admin) {
            $admin->loginAs(User::find(1)) //login as admin to generate navigator
                ->visit('navigators/'.$this->navigator->id) //shows first view of navigator.
                ->waitForText($this->navigator->title)
                ->assertSee($this->navigatorView->title)
                ->click('#add-navigator-items')
                ->waitForText(trans('global.create').' '.trans('global.navigator_item.title_singular'))
                ->select2('#referenceable_type', trans('global.referenceable_types.navigator_view'))
                ->type('title', $this->navigator_item['view']['title'])
                ->type('description', $this->navigator_item['view']['description'])
                ->select2('#medium_id', 1)
                ->select2('#position', trans('global.content'))
                ->select2('#css_class', 'col-xs-12')
                ->select2('#visibility', trans('global.navigator_item.fields.visibility_show'))
                ->click('#navigator-item-save')
                ->waitForText($this->navigator_item['view']['title'])
            // generate item curriculum
                ->visit('navigators/'.$this->navigator->id) //shows first view of navigator.
                ->waitForText($this->navigator->title)
                ->assertSee($this->navigatorView->title)
                ->click('#add-navigator-items')
                ->waitForText(trans('global.create').' '.trans('global.navigator_item.title_singular'))
                ->select2('#referenceable_type', trans('global.referenceable_types.curriculum'))
                ->select2('#referenceable_id', $this->navigator_item['curriculum']['title'])
                ->select2('#position', trans('global.content'))
                ->select2('#css_class', 'col-xs-12')
                ->select2('#visibility', trans('global.navigator_item.fields.visibility_show'))
                ->click('#navigator-item-save')
                ->waitForText($this->navigator_item['curriculum']['title']);
            // generate item content
            $admin->loginAs(User::find(1))
                ->visit('navigators/'.$this->navigator->id) //shows first view of navigator.
                ->waitForText($this->navigator->title)
                ->assertSee($this->navigatorView->title)
                ->click('#add-navigator-items')
                ->waitForText(trans('global.create').' '.trans('global.navigator_item.title_singular'))
                ->select2('#referenceable_type', trans('global.referenceable_types.content'))
                ->type('title', $this->navigator_item['content']['title'])
                ->type('description', $this->navigator_item['content']['description'])
                ->select2('#position', trans('global.header'))
                ->select2('#css_class', 'col-12')
                ->select2('#visibility', trans('global.navigator_item.fields.visibility_show'))
                ->click('#navigator-item-save')
                ->waitForText($this->navigator_item['content']['title']);
            // generate item medium
            $admin->loginAs(User::find(1))
                ->visit('navigators/'.$this->navigator->id) //shows first view of navigator.
                ->waitForText($this->navigator->title)
                ->assertSee($this->navigatorView->title)
                ->click('#add-navigator-items')
                ->waitForText(trans('global.create').' '.trans('global.navigator_item.title_singular'))
                ->select2('#referenceable_type', trans('global.referenceable_types.medium'))
                ->type('title', $this->navigator_item['medium']['title'])
                ->type('description', $this->navigator_item['medium']['description'])
                ->select2('#medium_id', 1)
                ->select2('#position', trans('global.content'))
                ->select2('#css_class', 'col-xs-12')
                ->select2('#visibility', trans('global.navigator_item.fields.visibility_show'))
                ->click('#navigator-item-save')
                ->waitForText($this->navigator_item['medium']['title']);
        });
    }

    /**
     * How Navigator
     *
     * @return void
     */
    public function testShowNavigator()
    {
        $this->browse(function (Browser $guest) {
            $guest->loginAs(User::find(8))
                ->visit("navigators/{$this->navigator->id}")
                ->waitForText($this->navigator->title)
                ->waitForText($this->navigatorView->title)
                ->screenshot('usecases/guest/navigator');
            // open view item
            $navigator_view_item = NavigatorItem::where('title', $this->navigator_item['view']['title'])->first();
            $guest->click('#navigator-item-'.$navigator_view_item->id)
                ->waitForText($navigator_view_item->title)
                ->assertSee($navigator_view_item->description)
                ->screenshot('usecases/guest/navigator_view_item');

            // open curriculum item
            $curriculum = Curriculum::find(1);
            $navigator_curriculum_item = NavigatorItem::where('title', $this->navigator_item['curriculum']['title'])->first();
            $guest->visit("navigators/{$this->navigator->id}")
                ->waitForText($this->navigator->title)
                ->click('#navigator-item-'.$navigator_curriculum_item->id)
                ->waitForText($curriculum->title)
                ->visit('navigators/'.$this->navigator->id) //shows first view of navigator.
                ->waitForText($curriculum->title)
                ->screenshot('usecases/guest/navigator_curriculum_item');
        });
    }
}
