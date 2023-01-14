<?php

namespace Tests\Feature;

use App\Curriculum;
use App\TerminalObjective;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TerminalObjectiveCRUDTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();

        $this->post('curricula', $attributes = Curriculum::factory()->raw());
    }

    /** @test
     * Use Route: GET|HEAD, terminalObjectives, terminalObjectives.index
     */
    public function a_user_can_retrive_terminalObjectives()
    {

        //dd(\App\TerminalObjective::All());
    }

    /** @test
     * Use Route: POST, terminalObjectives, terminalObjectives.store
     */
    public function an_administrator_create_an_terminal_objective()
    {
        $this->post('terminalObjectives', $attributes = TerminalObjective::factory()->raw());

        $this->assertDatabaseHas('terminal_objectives', $attributes);
    }
}
