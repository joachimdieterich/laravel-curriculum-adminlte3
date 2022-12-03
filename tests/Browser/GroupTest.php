<?php

namespace Tests\Browser;

use App\Group;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class GroupTest extends DuskTestCase
{
    /**
     * Add group
     *
     * @return void
     */
    public function testAddGroup()
    {
        $this->browse(function (Browser $browser) {
            $group = Group::first();
            $new_group = Group::factory()->raw();
            $browser->loginAs(User::find(1))
                    ->visit(new Pages\GroupPage)
                    ->waitForText($group->title)
                    ->assertSee('Add Group')
                    ->click('#add-group')
                    ->type('title', $new_group['title'])
                    ->select2('#grade_id', '2. Klasse')
                    ->select2('#period_id', 'Test')
                    ->select2('#organization_id', 'curriculumonline')
                    ->click('#group-save')
                    ->waitForText($new_group['title'])
                    //->screenshot('see-group-details')
;
        });
    }

    /**
     * Edit group
     *
     * @return void
     */
    public function testShowGroup()
    {
        $this->browse(function (Browser $admin) {
            $group = Group::find(1)->get()->first();
            $admin->loginAs(User::find(1))
                ->visit(new Pages\GroupPage)
                ->waitForText($group->title)
                ->click('#show-group-'.$group->id)
                //->screenshot('see-groupal-details')
                ->waitForText($group->title)
                ->assertSee($group->grade->title)
                ->assertSee($group->period->title)
                ->assertSee($group->organization->title)
               //->screenshot('see-group-details')
;
        });
    }

    /**
     * Edit group
     *
     * @return void
     */
    public function testEditGroup()
    {
        $this->browse(function (Browser $admin) {
            $new_group = Group::factory()->raw();
            $group = Group::find(1)->get()->first();
            $admin->loginAs(User::find(1))
                    ->visit(new Pages\GroupPage)
                    ->waitForText($group->title)
                    ->click('#edit-group-'.$group->id)
                    ->waitForText(trans('global.edit').' '.trans('global.group.title_singular'))
                    ->type('title', $group->title.' changed')
                    ->click('#group-save')
                    ->waitForText($group->title.' changed')
                    //->screenshot('see-group')
;
        });
    }

    /**
     * Delete group
     *
     * @return void
     */
    public function testDeleteGroup()
    {
        $this->browse(function (Browser $admin) {
            $new_group = Group::factory()->raw();
            $group = Group::create($new_group);

            $admin->loginAs(User::find(1))
                    ->visit(new Pages\GroupPage)
                    ->waitForText($group->title)
                    ->click('#delete-group-'.$group->id)
                    ->waitForText(trans('global.group.title_singular').' '.trans('global.list'))
                    ->assertDontSee($group->title)
                   // ->screenshot('see-group')
;
        });
    }
}
