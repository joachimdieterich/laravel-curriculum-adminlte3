<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ProgressCRUDTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        //$this->signIn();
        $this->signInAdmin();
        $this->post('curricula', $attributes = factory('App\Curriculum')->raw());
        $this->post('terminalObjectives', factory('App\TerminalObjective')->raw());
        //generate some objectives ID 1-10
        $this->post('enablingObjectives', factory('App\EnablingObjective')->raw());
        $this->post('enablingObjectives', factory('App\EnablingObjective')->raw());
        $this->post('enablingObjectives', factory('App\EnablingObjective')->raw());
        $this->post('enablingObjectives', factory('App\EnablingObjective')->raw());
        $this->post('enablingObjectives', factory('App\EnablingObjective')->raw());
        $this->post('enablingObjectives', factory('App\EnablingObjective')->raw());
        $this->post('enablingObjectives', factory('App\EnablingObjective')->raw());
        $this->post('enablingObjectives', factory('App\EnablingObjective')->raw());
        $this->post('enablingObjectives', factory('App\EnablingObjective')->raw());
        $this->post('enablingObjectives', factory('App\EnablingObjective')->raw());
    }

    /** @test
     * Use Route: POST, progresses, progresses.store
     */
    public function the_progress_value_can_be_calculated_for_an_terminalObjective()
    {
        //$this->withoutExceptionHandling();
//        $attributes = factory('App\Achievement')->raw();
//        $attributes['owner_id'] = auth()->user()->id;
//        $attributes['user_id'] = 2; //set achievement for studend (id 2)
//
//        $attributes['referenceable_type'] = 'App\\EnablingObjective';
//        $attributes['referenceable_id'] = 1;
//        $attributes['status'] = 01;
//        //dd($attributes);
//        $this->post("achievements" , $attributes);
//
//
//         $this->followingRedirects()->post("/progresses" , $progress_attributes = [
//                    'referencable_type' => 'App\TerminalObjective',
//                    'referencable_id' => 1,
//                    'associable_type' => 'App\User',
//                    'associable_id' => 1,
//                ])
//                ->assertStatus(200);
//        $this->assertDatabaseHas('progresses', $progress_attributes);
    }
}
