<?php

namespace Tests\Feature;

use App\Curriculum;
use App\TerminalObjective;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurriculumSyncObjectiveTypesOrderTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();
    }

    /** @test
     *
     * Use Route: POST, curricula/{curriculum}|syncObjectiveTypesOrder, curricula.syncObjectiveTypesOrder
     */
    public function the_owner_of_a_curriculum_can_order_the_objective_types()
    {
        // Create Curriculum with three terminalObjectives (with different objectiveTypes)
        $curriculum = $this->create_terminal_Objectives_with_different_objective_types();

        $this->put("/curricula/$curriculum->id/syncObjectiveTypesOrder", ['objective_type_order' => [3, 2, 1]])
             ->assertStatus(200);

        $this->assertDatabaseHas('curricula', [
            'id' => $curriculum->id,
            'objective_type_order' => $this->castAsJson([3, 2, 1]),
        ]);
    }

    /** @test
     *
     * Use Route: POST, curricula/{curriculum}|syncObjectiveTypesOrder, curricula.syncObjectiveTypesOrder
     */
    public function a_user_who_does_not_own_a_curriculum_can_not_order_the_objective_types()
    {
        // Create Curriculum with three terminalObjectives (with different objectiveTypes)
        $curriculum = $this->create_terminal_Objectives_with_different_objective_types();

        $this->assertDatabaseHas('curricula', [
            'id' => $curriculum->id,
            'objective_type_order' => null,
        ]);

        $this->actingAs(User::find(4)); //Act as Schooladmin (who is not owner of curriculum)

        $this->put("/curricula/$curriculum->id/syncObjectiveTypesOrder", ['objective_type_order' => [3, 2, 1]])
            ->assertStatus(403);

        $this->assertDatabaseHas('curricula', [
            'id' => $curriculum->id,
            'objective_type_order' => null,
        ]);
    }

    private function create_terminal_Objectives_with_different_objective_types()
    {
        $this->post('curricula', $attributes = Curriculum::factory()->raw());
        $curriculum = Curriculum::where('title', $attributes['title'])->first();
        $terminalObjective1 = TerminalObjective::factory()->raw();
        $this->post('terminalObjectives', $terminalObjective1);
        $terminalObjective2 = TerminalObjective::factory()->raw();
        $terminalObjective2['objective_type_id'] = 2;
        $this->post('terminalObjectives', $terminalObjective2);
        $terminalObjective3 = TerminalObjective::factory()->raw();
        $terminalObjective3['objective_type_id'] = 3;
        $this->post('terminalObjectives', $terminalObjective3);

        return $curriculum;
    }
}
