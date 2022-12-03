<?php

namespace Tests\Api;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_client_can_not_get_users()
    {
        $this->get('/api/v1/users')->assertStatus(302);
        $this->stringContains('login');
    }

    /** @test
     * Use Route: GET, /api/v1/users
     */
    public function an_authenticated_client_can_get_all_users()
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/users')
             ->assertStatus(200)
             ->assertJson(User::all()->toArray());
    }

    /** @test
     * Use Route: GET, /api/v1/users/{id}
     */
    public function an_authenticated_client_can_get_an_user()
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/users/1')
             ->assertStatus(200)
             ->assertJson(User::find(1)->toArray());
    }

    /** @test
     * Use Route: POST, /api/v1/users
     */
    public function an_authenticated_client_can_create_an_user()
    {
        $this->signInApiAdmin();
        $date = date('Y-m-d H:i:s');
        $this->post('/api/v1/users', $attributes = [
            'username' => 'username',
            'common_name' => 'cn_username',
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'email' => 'username@curriclumonline.de',
            'email_verified_at' => $date,
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('users', [
            'username' => 'username',
            'common_name' => 'cn_username',
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'email' => 'username@curriclumonline.de',
            'email_verified_at' => $date,
        ]);
    }

    /** @test
     * Use Route: PUT, /api/v1/users/{id}
     */
    public function an_authenticated_client_can_update_an_user()
    {
        $this->signInApiAdmin();

        $date = date('Y-m-d H:i:s');
        $new_user = $this->post('/api/v1/users', $attributes = [
            'username' => 'username',
            'common_name' => 'cn_username',
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'email' => 'username@curriclumonline.de',
            'email_verified_at' => $date,
            'password' => 'password', // password
        ]);

        $this->put("/api/v1/users/{$new_user->getData()->id}",
                        ['firstname' => 'newfirstname',
                            'lastname' => 'newlastname', ]
                  );

        $this->assertDatabaseHas('users', [
            'username' => 'username',
            'common_name' => 'cn_username',
            'firstname' => 'newfirstname',
            'lastname' => 'newlastname',
            'email' => 'username@curriclumonline.de',
            'email_verified_at' => $date,
        ]);
    }

    /** @test
     * Use Route: DELETE, /api/v1/users/{id}
     */
    public function an_authenticated_client_can_delete_an_user()
    {
        $this->signInApiAdmin();
        $date = date('Y-m-d H:i:s');
        $new_user = $this->post('/api/v1/users', $attributes = [
            'username' => 'username',
            'common_name' => 'cn_username',
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'email' => 'username@curriclumonline.de',
            'email_verified_at' => $date,
            'password' => 'password',
        ]);

        $this->delete("/api/v1/users/{$new_user->getData()->id}");

        $this->assertDatabaseMissing('users', [
            'username' => 'username',
            'common_name' => 'cn_username',
            'firstname' => 'newfirstname',
            'lastname' => 'newlastname',
            'email' => 'username@curriclumonline.de',
            'email_verified_at' => $date,
        ]);
    }

    /** @test
     * Use Route: POST, /api/v1/users/{user}/force
     */
    public function an_authenticated_client_can_force_delete_an_user()
    {
        $this->signInApiAdmin();
        $date = date('Y-m-d H:i:s');
        $new_user = $this->post('/api/v1/users', $attributes = [
            'username' => 'username',
            'common_name' => 'cn_username',
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'email' => 'username@curriclumonline.de',
            'email_verified_at' => $date,
            'password' => 'password',
        ]);

        $this->delete("/api/v1/users/{$new_user->getData()->id}/force");

        $this->assertNull(User::where('id', $new_user->getData()->id)->withTrashed()->get()->first());
    }

    /** @test
     * Use Route: POST, /api/v1/users/{user}/groups
     */
    public function an_authenticated_client_can_get_groups_of_an_user()
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/users/1/groups')
             ->assertStatus(200)
             ->assertSee(json_decode(htmlspecialchars(User::where('id', 1)->with('groups')->get())));
    }

    /** @test
     * Use Route: POST, /api/v1/users/{user}/organizations
     */
    public function an_authenticated_client_can_get_organizations_of_an_user()
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/users/1/organizations')
            ->assertStatus(200)
            ->assertSee(json_decode(htmlspecialchars(User::where('id', 1)->with('organizations')->get())));
    }

    /** @test
     * Use Route: POST, /api/v1/users/{user}/roles
     */
    public function an_authenticated_client_can_get_roles_of_an_user()
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/users/1/roles')
            ->assertStatus(200)
            ->assertSee(json_decode(htmlspecialchars(User::where('id', 1)->with('roles')->get())));
    }
}
