<?php

namespace Tests\Feature;

use App\Organization;
use App\OrganizationRoleUser;
use App\Period;
use App\Role;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ManageUserTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();
    }

    /** @test */
    public function an_administrator_can_enrol_an_user_to_an_organization_with_a_role()
    {
        $role_admin_id = 1;
        $role_creator_id = 2;
        $org1 = Organization::factory()->create();
        $org2 = Organization::factory()->create();

        $this->post('/organizations/enrol', ['enrollment_list' => [
            ['user_id' => auth()->user()->id,
                'organization_id' => $org1->id,
                'role_id' => $role_admin_id,
            ],
        ],
        ]);

        $this->assertDatabaseHas('organization_role_users', [
            'user_id' => auth()->user()->id,
            'organization_id' => $org1->id,
            'role_id' => $role_admin_id,
        ]);

        $this->post('/organizations/enrol', ['enrollment_list' => [
            ['user_id' => auth()->user()->id,
                'organization_id' => $org2->id,
                'role_id' => $role_creator_id,
            ],
        ],
        ]);
        $this->assertDatabaseHas('organization_role_users', [
            'user_id' => auth()->user()->id,
            'organization_id' => $org2->id,
            'role_id' => $role_creator_id,
        ]);

        // Check for Integrity constraint violation
        try {
            $this->post('/organizations/enrol', ['enrollment_list' => [
                ['user_id' => auth()->user()->id,
                    'organization_id' => $org2->id,
                    'role_id' => $role_creator_id,
                ],
            ],
            ]);
        } catch (QueryException $e) {
            $this->assertStringContainsString('Integrity constraint violation', $e->getMessage());
        }
    }

    /** @test */
    public function every_user_has_a_role()
    {
        $this->post('/roles', $role = Role::factory()->raw());
        $role_id = Role::where('title', $role['title'])->first()->id;

        $organization = Organization::factory()->create();

        $user_to_enrol = User::factory()->create();
        OrganizationRoleUser::firstOrCreate([
            'organization_id' => $organization->id,
            'user_id' => $user_to_enrol->id,
            'role_id' => $role_id,
        ]);

        $this->assertDatabaseHas('organization_role_users', [
            'user_id' => $user_to_enrol->id,
            'organization_id' => $organization->id,
            'role_id' => $role_id,
        ]);
        $this->assertEquals($role['title'], $user_to_enrol->roles->first()->title);
    }

    /** @test */
    public function a_user_can_be_expelled_from_an_organization_by_the_admin()
    {
        $this->withoutExceptionHandling();
        $this->post('/roles', $role = Role::factory()->raw());
        $role_id = Role::where('title', $role['title'])->first()->id;

        $organization = Organization::factory()->create();

        $user_expel = User::factory()->create();
        OrganizationRoleUser::firstOrCreate([
            'organization_id' => $organization->id,
            'user_id' => $user_expel->id,
            'role_id' => $role_id,
        ]);

        $this->assertDatabaseHas('organization_role_users', [
            'user_id' => $user_expel->id,
            'organization_id' => $organization->id,
            'role_id' => $role_id,
        ]);
        $this->assertEquals($role['title'], $user_expel->roles->first()->title);

        $this->followingRedirects()->delete('/organizations/expel', $attributes = $attributes = [
            'expel_list' => [
                ['user_id' => auth()->user()->id,
                    'organization_id' => $organization->id,
                ],
            ],
        ])
                ->assertStatus(200);
    }

    /** @test */
    public function an_admin_can_mass_update_user_passwords()
    {
        $users = User::factory(50)->create();
        $ids = $users->pluck('id')->toArray();

        $new_password = Hash::make('new_password');
        $this->patch('/users/massUpdate', $attributes = [
            'ids' => $ids,
            'password' => $new_password,
        ])->assertStatus(204);

        foreach ($ids as $id) {
            $this->assertTrue(\Hash::check($new_password, User::find($id)->password));
        }
    }

    /** @test */
    public function an_admin_can_mass_update_user_status()
    {
        $users = User::factory(50)->create();
        $ids = $users->pluck('id')->toArray();

        $this->patch('/users/massUpdate', $attributes = [
            'ids' => $ids,
            'status_id' => 2,
        ])->assertStatus(204);

        foreach ($ids as $id) {
            $this->assertEquals(2, User::find($id)->status_id);
        }
    }

    /** @test */
    public function a_user_can_update_its_current_organization_id()
    {
        $org1 = Organization::factory()->create();
        $period = Period::factory()->create();

        $this->patch('users/setCurrentOrganization',
                [
                    'current_organization_id' => $org1->id,

                ],
          );

        $this->assertDatabaseHas('users', [
            'id' => auth()->user()->id,
            'current_organization_id' => $org1->id,
        ]);
    }

    /** @test */
    public function a_user_can_update_its_current_period_id()
    {
        $org1 = Organization::factory()->create();
        $period = Period::factory()->create();

        $this->patch('users/setCurrentPeriod',
                [
                    'current_period_id' => $period->id,

                ],
          );

        $this->assertDatabaseHas('users', [
            'id' => auth()->user()->id,
            'current_period_id' => $period->id,
        ]);
    }
}
