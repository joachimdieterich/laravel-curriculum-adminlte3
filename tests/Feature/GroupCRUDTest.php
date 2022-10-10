<?php

namespace Tests\Feature;

use App\Curriculum;
use App\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupCRUDTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();
    }

    /** @test
     * Use Route: GET|HEAD, groups, groups.index
     */
    public function an_administrator_see_groups()
    {
        $this->get('groups')
             ->assertStatus(200);

        /* Use Datatables */
        $groups = Group::first();
        $this->get('groups/list')
             ->assertStatus(200)
             ->assertViewHasAll(compact($groups));
    }

    /** @test
     * Use Route: POST, groups, groups.index
     */
    public function an_administrator_create_a_group()
    {
        $attributes = factory('App\Group')->raw();

        $this->post('groups', $attributes)
                ->assertStatus(302);

        $this->assertDatabaseHas('groups', $attributes);
    }

    /** @test
     * Use Route: POST, groups, groups.index
     */
    public function an_administrator_get_create_view_for_a_group()
    {
        $this->get('groups/create')
             ->assertStatus(200);
    }

    /** @test
     * Use Route: DELETE, groups/massDestroy groups.massDestroy
     */
    public function an_administrator_can_mass_delete_groups()
    {
        $this->post('groups', $group1 = factory('App\Group')->raw());
        $ids[] = Group::where('title', $group1['title'])->first()->id;

        $this->post('groups', $group2 = factory('App\Group')->raw());
        $ids[] = Group::where('title', $group2['title'])->first()->id;

        $this->delete('/groups/massDestroy', $attributes = [
            'ids' =>  $ids,
        ])->assertStatus(204);

        foreach ($ids as $id) {
            $this->assertDatabaseMissing('groups', [
                'id' => $id,
            ]);
        }
    }

    /** @test
     * Use Route: DELETE, groups/{group}, groups.destroy
     */
    public function an_administrator_delete_a_group()
    {
        $this->post('groups', $group = factory('App\Group')->raw());
        $id = Group::where('title', $group['title'])->first()->id;

        $this->followingRedirects()
                ->delete('groups/'.$id)
                ->assertStatus(200);
    }

    /** @test
     * Use Route: GET|HEAD, groups/{group}, groups.show
     */
    public function an_administrator_see_details_of_an_group()
    {
        $this->post('groups', $group = factory('App\Group')->raw());

        $curriculum = factory(Curriculum::class)->create();

        $group = Group::where('title', $group['title'])->first();
        Group::findOrFail($group->id)->curricula()->syncWithoutDetaching([$curriculum->id]);

        $group = Group::where('title', $group['title'])->with('curricula')->first();

        $this->get("groups/{$group->id}")
             ->assertStatus(200)
             ->assertViewHasAll(compact($group));
    }

    /** @test
     * Use Route: PUT|PATCH, groups/{group}, groups.update
     */
    public function an_administrator_update_a_group()
    {
        $this->withoutExceptionHandling();
        $this->post('groups', $group = factory('App\Group')->raw());
        $group = Group::where('title', $group['title'])->first()->toArray();

        $this->assertDatabaseHas('groups', $group);
        $this->patch('groups/'.$group['id'], $new_attributes = factory('App\Group')->raw());
        $group_edit = Group::where('title', $new_attributes['title'])->first()->toArray();

        $this->assertDatabaseHas('groups', $group_edit);
    }

    /** @test
     * Use Route: GET|HEAD, groups/{group}/edit, groups.edit
     */
    public function an_administrator_get_edit_view_for_a_group()
    {
        $this->post('groups', $group = factory('App\Group')->raw());
        $group = Group::where('title', $group['title'])->first();
        $this->withoutExceptionHandling();
        $this->get("groups/{$group->id}/edit")
             ->assertStatus(200)
             ->assertSessionHasAll(compact($group));
    }
}
