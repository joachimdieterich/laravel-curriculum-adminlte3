<?php

namespace Tests\Feature;

use App\Organization;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();
    }

    /** @test
     *
     * Use Route: POST, organizations/enrol, organizations.enrol
     */
    public function an_administrator_can_enrol_multiple_users_to_existing_organizations_with_an_existing_role()
    {
        $organization1 = Organization::factory()->create();
        $organization2 = Organization::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $enrollment_list = [
            ['user_id' => $user1->id,
                'organization_id' => $organization1->id,
                'role_id' => 1,                             // 1 == Admin
            ],
            ['user_id' => $user1->id,
                'organization_id' => $organization2->id,
                'role_id' => 6,                             // 6 == Student
            ],
            ['user_id' => $user2->id,
                'organization_id' => $organization1->id,
                'role_id' => 4,                             // 4 == Schooladmin
            ],
            ['user_id' => $user2->id,
                'organization_id' => $organization2->id,
                'role_id' => 5,                             // 5 == Teacher
            ],
        ];

        $this->post('organizations/enrol', $attributes = [
            'enrollment_list' => $enrollment_list,
        ]);

        foreach ($enrollment_list as $entry) {
            $this->assertDatabaseHas('organization_role_users', [
                'user_id' => $entry['user_id'],
                'organization_id' => $entry['organization_id'],
                'role_id' => $entry['role_id'],
            ]);
        }
    }

    /** @test */
    public function an_administrator_can_expel_multiple_users_from_existing_organizations()
    {
        $organization1 = Organization::factory()->create();
        $organization2 = Organization::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $enrollment_list = [
            ['user_id' => $user1->id,
                'organization_id' => $organization1->id,
                'role_id' => 1,                             // 1 == Admin
            ],
            ['user_id' => $user1->id,
                'organization_id' => $organization2->id,
                'role_id' => 6,                             // 6 == Student
            ],
            ['user_id' => $user2->id,
                'organization_id' => $organization1->id,
                'role_id' => 4,                             // 4 == Schooladmin
            ],
            ['user_id' => $user2->id,
                'organization_id' => $organization2->id,
                'role_id' => 5,                             // 5 == Teacher
            ],
        ];

        $this->post('organizations/enrol', $attributes = [
            'enrollment_list' => $enrollment_list,
        ]);

        foreach ($enrollment_list as $entry) {
            $this->assertDatabaseHas('organization_role_users', [
                'user_id' => $entry['user_id'],
                'organization_id' => $entry['organization_id'],
                'role_id' => $entry['role_id'],
            ]);
        }

        $expel_list = [
            ['user_id' => $user1->id,
                'organization_id' => $organization1->id,
            ],
            ['user_id' => $user1->id,
                'organization_id' => $organization2->id,
            ],
            ['user_id' => $user2->id,
                'organization_id' => $organization1->id,
            ],
            ['user_id' => $user2->id,
                'organization_id' => $organization2->id,
            ],
        ];

        /* expel */
        $this->delete('organizations/expel', $attributes = [
            'expel_list' => $expel_list,
        ]);

        foreach ($expel_list as $entry) {
            $this->assertDatabaseMissing('organization_role_users', [
                'user_id' => $entry['user_id'],
                'organization_id' => $entry['organization_id'],
            ]);
        }
    }
}
