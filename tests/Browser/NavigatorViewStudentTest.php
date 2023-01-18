<?php

namespace Tests\Browser;

use App\Content;
use App\Curriculum;
use App\Medium;
use App\Navigator;
use App\NavigatorItem;
use App\NavigatorView;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class NavigatorViewStudentTest extends DuskTestCase
{
    /**
     * Edit navigator
     *
     * @return void
     */
    public function testShowNavigatorViewForStudentRole()
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

            // Item: view
            $navigatorView2 = NavigatorView::create([
                'title' => 'First View',
                'description' => 'First View Description',
                'navigator_id' => $navigator->id,
            ]);
            $navigator_item_view = NavigatorItem::firstOrCreate([
                'title' => 'View Item',
                'description' => 'Description',
                'navigator_view_id' => $navigatorView->id,
                'referenceable_type' => 'App\NavigatorView',
                'referenceable_id' => $navigatorView2->id,
                'position' => 'content',
                'css_class' => 'col-xs-12',
                'visibility' => 1,
            ]);

            //Item: curriculum
            $curriculum = Curriculum::find(1);
            $navigator_item_curriculum = NavigatorItem::firstOrCreate([
                'title' => $curriculum->title,
                'description' => $curriculum->description,
                'navigator_view_id' => $navigatorView->id,
                'referenceable_type' => 'App\Curriculum',
                'referenceable_id' => $curriculum->id,
                'position' => 'content',
                'css_class' => 'col-xs-12',
                'visibility' => 1,
            ]);
            if ($curriculum->medium_id != null) {
                $medium = Medium::find($curriculum->medium_id);
                $medium->subscribe($navigator_item_curriculum);
            }

            //Item:content
            $content = Content::firstOrCreate([
                'title' => 'Navigator content title',
                'content' => 'Navigator content description',
                'owner_id' => 1,
            ]);
            $navigator_item_content = NavigatorItem::firstOrCreate([
                'title' => $content->title,
                'description' => $content->content,
                'navigator_view_id' => $navigatorView->id,
                'referenceable_type' => 'App\Content',
                'referenceable_id' => $content->id,
                'position' => 'header',
                'css_class' => 'col-12',
                'visibility' => 1,
            ]);

            //Item: medium
            $navigator_item_medium = NavigatorItem::firstOrCreate([
                'title' => 'Navigator Medium Item Title',
                'description' => 'Navigator Medium Item description',
                'navigator_view_id' => $navigatorView->id,
                'referenceable_type' => 'App\Medium',
                'referenceable_id' => 1,
                'position' => 'content',
                'css_class' => 'col-xs-12',
                'visibility' => 1,
            ]);

            $admin->loginAs(User::find(2))
                ->visit('/navigators/'.$navigator->id.'/'.$navigatorView->id)
                ->waitForText($navigatorView->title)
                ->assertSee($navigatorView->description)
                ->screenshot('/student/see-navigatoral-details');
        });
    }
}
