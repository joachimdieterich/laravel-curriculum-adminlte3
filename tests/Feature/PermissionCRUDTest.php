<?php

namespace Tests\Feature;

use App\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionCRUDTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();
    }

    /** @test
     * Use Route: GET|HEAD, permissions, permissions.index
     */
    public function an_administrator_see_permissions()
    {
        $this->get('permissions')
            ->assertStatus(200);

        /* Use Datatables */
        $permissions = Permission::select('id', 'title')->get();
        $list = $this->get('permissions/list')
            ->assertStatus(200);
        $i = 0;
        foreach ($permissions as $permission) {
            if ($i === 49) {
                break;
            } //test max 50 entries (default page limit on datatables
            $list->assertJsonFragment($permission->toArray());
            $i++;
        }
    }

    /** @test
     * Use Route: POST, permissions, permissions.index
     */
    public function an_administrator_create_a_permission()
    {
        $this->followingRedirects()
            ->post('permissions', $attributes = Permission::factory()->raw())
            ->assertStatus(200);

        $this->assertDatabaseHas('permissions', $attributes);
    }

    /** @test
     * Use Route: POST, permissions, permissions.index
     */
    public function an_administrator_get_create_view_for_permissions()
    {
        $this->get('permissions/create')
             ->assertStatus(200);
    }

    /** @test
     * Use Route: DELETE, permissions/{permission}, permissions.destroy
     */
    public function an_administrator_delete_a_permission()
    {
        $permission = Permission::factory()->create();

        $this->followingRedirects()
            ->delete('permissions/'.$permission->id)
            ->assertStatus(200);

        $this->assertDatabaseMissing('permissions', [
            'id' => $permission->id,
            'title' => $permission->title,
            'created_at' => $permission->created_at,
            'updated_at' => $permission->updated_at,
            'deleted_at' => $permission->deleted_at,
        ]);
    }

    /** @test
     * Use Route: GET|HEAD, permissions/{permission}, permissions.show
     */
    public function an_administrator_see_details_of_a_permission()
    {
        $permission = Permission::factory()->create();

        $this->get("permissions/{$permission->id}")
            ->assertStatus(200)
            ->assertSee([
                'title' => $permission->title,
            ]);
    }

    /** @test
     * Use Route: PUT|PATCH, permissions/{permission}, permissions.update
     */
    public function an_administrator_update_a_permission()
    {
        $this->post('permissions', $attributes = Permission::factory()->raw());
        $permission = Permission::where('title', $attributes['title'])->first()->toArray();

        $this->assertDatabaseHas('permissions', $permission);

        $this->patch('permissions/'.$permission['id'], $new_attributes = Permission::factory()->raw());

        $permission_edit = Permission::where('title', $new_attributes['title'])->first()->toArray();
        $this->assertDatabaseHas('permissions', $permission_edit);
    }

    /** @test
     * Use Route: GET|HEAD, permissions/{permission}/edit, permissions.edit
     */
    public function an_administrator_get_edit_view_for_permissions()
    {
        $this->post('permissions', $attributes = Permission::factory()->raw());
        $permission = Permission::where('title', $attributes['title'])->first();

        $this->get("permissions/{$permission->id}/edit")
            ->assertStatus(200)
            ->assertSee([
                'title' => $permission->title,
            ]);
    }
}
