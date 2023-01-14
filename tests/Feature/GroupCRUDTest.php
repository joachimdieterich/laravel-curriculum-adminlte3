<?php

namespace Tests\Feature;

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
        $groups = Group::select('id', 'title')->get();
        $list = $this->get('groups/list')
            ->assertStatus(200);
        $i = 0;
        foreach ($groups as $group) {
            if ($i === 49) {
                break;
            } //test max 50 entries (default page limit on datatables
            $list->assertJsonFragment($group->toArray());
            $i++;
        }
    }

    /** @test
     * Use Route: POST, groups, groups.index
     */
    public function an_administrator_create_a_group()
    {
        $this->followingRedirects()
            ->post('groups', $attributes = Group::factory()->raw())
            ->assertStatus(200);

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
        $groups = Group::factory(50)->create();
        $ids = $groups->pluck('id')->toArray();

        $this->delete('/groups/massDestroy', $attributes = [
            'ids' => $ids,
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
        $group = Group::factory()->create();

        $this->followingRedirects()
            ->delete('groups/'.$group->id)
            ->assertStatus(200);

        $this->assertDatabaseMissing('groups', $group->toArray());
    }

    /** @test
     * Use Route: GET|HEAD, groups/{group}, groups.show
     */
    public function an_administrator_see_details_of_an_group()
    {
        $group = Group::factory()->create();

        $this->get("groups/{$group->id}")
            ->assertStatus(200)
            ->assertSee($group->toArray());
    }

    /** @test
     * Use Route: PUT|PATCH, groups/{group}, groups.update
     */
    public function an_administrator_update_a_group()
    {
        $this->post('groups', $attributes = Group::factory()->raw());
        $group = Group::where('title', $attributes['title'])->first()->toArray();

        $this->assertDatabaseHas('groups', $group);

        $this->patch('groups/'.$group['id'], $new_attributes = Group::factory()->raw());

        $group_edit = Group::where('title', $new_attributes['title'])->first()->toArray();
        $this->assertDatabaseHas('groups', $group_edit);
    }

    /** @test
     * Use Route: GET|HEAD, groups/{group}/edit, groups.edit
     */
    public function an_administrator_get_edit_view_for_a_group()
    {
        $this->post('groups', $attributes = Group::factory()->raw());
        $group = Group::where('title', $attributes['title'])->first();

        $this->get("groups/{$group->id}/edit")
            ->assertStatus(200)
            ->assertSee([
                'title' => $group->title,
            ]);
    }
}
