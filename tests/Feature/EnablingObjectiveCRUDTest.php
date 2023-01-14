<?php

namespace Tests\Feature;

use App\Curriculum;
use App\EnablingObjective;
use App\TerminalObjective;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnablingObjectiveCRUDTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();
        /* add curriculum needed by tests*/
        $this->post('curricula', $attributes = Curriculum::factory()->raw());
        $this->post('terminalObjectives', TerminalObjective::factory()->raw());
    }

    /** @test
     * Use Route: POST, enablingObjectives, enablingObjectives.store
     */
    public function an_administrator_create_an_enabling_objective()
    {
        $attributes = EnablingObjective::factory()->raw();

        $this->post('enablingObjectives', $attributes);
        $this->assertDatabaseHas('enabling_objectives', $attributes);
    }

    /** @test
     * Use Route: GET|HEAD, enablingObjectives/{enablingObjective}, enablingObjectives.show
     */
    public function an_administrator_see_details_of_an_enablingObjective()
    {
        $enablingObjective = EnablingObjective::factory()->create();

        $this->get("enablingObjectives/{$enablingObjective->id}")
             ->assertStatus(200)
             ->assertSee($enablingObjective->toArray());
    }
}
