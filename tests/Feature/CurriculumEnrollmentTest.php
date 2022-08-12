<?php

namespace Tests\Feature;

use App\Curriculum;
use App\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurriculumEnrollmentTest extends TestCase
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
    public function an_administrator_can_enrol_multiple_groups_to_existing_curricula()
    {
        $group1 = Group::factory()->create();
        $group2 = Group::factory()->create();
        $curriculum1 = Curriculum::factory()->create();
        $curriculum2 = Curriculum::factory()->create();

        $enrollment_list = [
            ['curriculum_id' => $curriculum1->id,
                'group_id' => $group1->id,
            ],
            ['curriculum_id' => $curriculum1->id,
                'group_id' => $group2->id,
            ],
            ['curriculum_id' => $curriculum2->id,
                'group_id' => $group1->id,
            ],
            ['curriculum_id' => $curriculum2->id,
                'group_id' => $group2->id,
            ],
        ];

        $this->post('curricula/enrol', $attributes = [
            'enrollment_list' => $enrollment_list,
        ]);

        foreach ($enrollment_list as $entry) {
            $this->assertDatabaseHas('curriculum_group', [
                'curriculum_id' => $entry['curriculum_id'],
                'group_id' => $entry['group_id'],
            ]);
        }
    }

    /** @test */
    public function an_administrator_can_expel_multiple_groups_to_existing_curricula()
    {
        $group1 = Group::factory()->create();
        $group2 = Group::factory()->create();
        $curriculum1 = Curriculum::factory()->create();
        $curriculum2 = Curriculum::factory()->create();

        $enrollment_list = [
            ['curriculum_id' => $curriculum1->id,
                'group_id' => $group1->id,
            ],
            ['curriculum_id' => $curriculum1->id,
                'group_id' => $group2->id,
            ],
            ['curriculum_id' => $curriculum2->id,
                'group_id' => $group1->id,
            ],
            ['curriculum_id' => $curriculum2->id,
                'group_id' => $group2->id,
            ],
        ];

        $this->post('curricula/enrol', $attributes = [
            'enrollment_list' => $enrollment_list,
        ]);

        foreach ($enrollment_list as $entry) {
            $this->assertDatabaseHas('curriculum_group', [
                'curriculum_id' => $entry['curriculum_id'],
                'group_id' => $entry['group_id'],
            ]);
        }

        /* expel */
        $this->delete('curricula/expel', ['expel_list' => $enrollment_list]);

        foreach ($enrollment_list as $entry) {
            $this->assertDatabaseMissing('curriculum_group', [
                'curriculum_id' => $entry['curriculum_id'],
                'group_id' => $entry['group_id'],
            ]);
        }
    }
}
