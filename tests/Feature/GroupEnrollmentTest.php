<?php

namespace Tests\Feature;

use App\Group;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();
    }

    /** @test
     *
     * Use Route: POST, groups/enrol, groups.enrol
     */
    public function an_administrator_can_enrol_multiple_users_to_existing_groups()
    {
        $this->withoutExceptionHandling();
        $group1 = Group::factory()->create();
        $group2 = Group::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $enrollment_list = [
            ['user_id' => $user1->id,
                'group_id' => $group1->id,
            ],
            ['user_id' => $user1->id,
                'group_id' => $group2->id,
            ],
            ['user_id' => $user2->id,
                'group_id' => $group1->id,
            ],
            ['user_id' => $user2->id,
                'group_id' => $group2->id,
            ],
        ];

        $this->post('groups/enrol', $attributes = [
            'enrollment_list' => $enrollment_list,
        ]);

        foreach ($enrollment_list as $entry) {
            $this->assertDatabaseHas('group_user', [
                'user_id' => $entry['user_id'],
                'group_id' => $entry['group_id'],
            ]);
        }
    }

    /** @test */
    public function an_administrator_can_expel_multiple_users_from_existing_groups()
    {
        $group1 = Group::factory()->create();
        $group2 = Group::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $enrollment_list = [
            ['user_id' => $user1->id,
                'group_id' => $group1->id,
            ],
            ['user_id' => $user1->id,
                'group_id' => $group2->id,
            ],
            ['user_id' => $user2->id,
                'group_id' => $group1->id,
            ],
            ['user_id' => $user2->id,
                'group_id' => $group2->id,
            ],
        ];

        $this->post('groups/enrol', $attributes = [
            'enrollment_list' => $enrollment_list,
        ]);

        foreach ($enrollment_list as $entry) {
            $this->assertDatabaseHas('group_user', [
                'user_id' => $entry['user_id'],
                'group_id' => $entry['group_id'],
            ]);
        }

        /* expel */
        $this->delete('groups/expel', ['expel_list' => $enrollment_list]);

        foreach ($enrollment_list as $entry) {
            $this->assertDatabaseMissing('group_user', [
                'user_id' => $entry['user_id'],
                'group_id' => $entry['group_id'],
            ]);
        }
    }
}
