<?php

namespace Tests\Feature;

use App\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleCRUDTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();
    }

    /** @test
     * Use Route: GET|HEAD, roles, roles.index
     */
    public function an_administrator_see_roles()
    {
        $this->get('roles')
             ->assertStatus(200);

        /* Use Datatables */
        $roles = Role::select('id', 'title')->get();
        $list = $this->get('roles/list')
            ->assertStatus(200);
        $i = 0;
        foreach ($roles as $role) {
            if ($i === 49) {
                break;
            } //test max 50 entries (default page limit on datatables
            $list->assertJsonFragment($role->toArray());
            $i++;
        }
    }

    /** @test
     * Use Route: POST, roles, roles.index
     */
    public function an_administrator_create_a_role()
    {
        $this->followingRedirects()
            ->post('roles', $attributes = Role::factory()->raw())
            ->assertStatus(200);

        $this->assertDatabaseHas('roles', [
            'title' => $attributes['title'],
        ]);
    }

    /** @test
     * Use Route: POST, roles, roles.index
     */
    public function an_administrator_get_create_view_for_roles()
    {
        $this->get('roles/create')
             ->assertStatus(200);
    }

    /** @test
     * Use Route: DELETE, roles/{role}, roles.destroy
     */
    public function an_administrator_delete_a_role()
    {
        $this->followingRedirects()
            ->post('roles', $attributes = Role::factory()->raw())
            ->assertStatus(200);

        $role = Role::where('title', $attributes['title'])->first();

        $this->followingRedirects()
            ->delete('roles/'.$role['id'])
            ->assertStatus(200);

        $this->assertDatabaseMissing('roles', $role->toArray());
    }

    /** @test
     * Use Route: GET|HEAD, roles/{role}, roles.show
     */
    public function an_administrator_see_details_of_a_role()
    {
        $this->followingRedirects()
            ->post('roles', $attributes = Role::factory()->raw())
            ->assertStatus(200);

        $role = Role::where('title', $attributes['title'])->first();

        $this->get("roles/{$role['id']}")
            ->assertStatus(200)
            ->assertSee(['title' => $role['title']]);
    }

    /** @test
     * Use Route: PUT|PATCH, roles/{role}, roles.update
     */
    public function an_administrator_update_a_role()
    {
        $this->post('roles', $attributes = Role::factory()->raw());
        $group = Role::where('title', $attributes['title'])->first()->toArray();

        $this->assertDatabaseHas('roles', $group);

        $this->patch('roles/'.$group['id'], $new_attributes = Role::factory()->raw());

        $role_edit = Role::where('title', $new_attributes['title'])->first()->toArray();
        $this->assertDatabaseHas('roles', $role_edit);
    }

    /** @test
     * Use Route: GET|HEAD, roles/{role}/edit, roles.edit
     */
    public function an_administrator_get_edit_view_for_roles()
    {
        $this->post('roles', $attributes = Role::factory()->raw());
        $role = Role::where('title', $attributes['title'])->first();

        $this->get("roles/{$role->id}/edit")
            ->assertStatus(200)
            ->assertSee([
                'title' => $role->title,
            ]);
    }
}
