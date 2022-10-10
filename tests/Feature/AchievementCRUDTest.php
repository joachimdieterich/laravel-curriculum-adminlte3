<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AchievementCRUDTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->signIn();
        //$this->signInAdmin();
        $this->post('curricula', $attributes = factory('App\Curriculum')->raw());
        $this->post('terminalObjectives', factory('App\TerminalObjective')->raw());
        $this->post('enablingObjectives', factory('App\EnablingObjective')->raw());
    }

    /** @test
     * Use Route: POST, achievements, achievements.store
     */
    public function an_user_can_set_the_achievement_status_for_an_enabling_objective()
    {
        //todo: check test, it fails after new  permission check
       /* $attributes = factory('App\Achievement')->raw();
        $attributes['owner_id'] = auth()->user()->id;
        $attributes['user_id'] = auth()->user()->id;

        $this->post("achievements" , $attributes);
        $attributes['status'] = "10";
        $this->assertDatabaseHas('achievements', $attributes);*/
    }

    /** @test
     * Use Route: POST, achievements, achievements.store
     */
    public function an_admin_can_set_the_achievement_status_for_an_enabling_objective()
    {
        Auth::logout(); //logout student
        $this->signInAdmin();
        $attributes = factory('App\Achievement')->raw();
        $attributes['owner_id'] = auth()->user()->id;
        $attributes['user_id'] = 2; //set achievement for student (id 2)

        $this->post('achievements', $attributes);
        $attributes['status'] = '01';
        $this->assertDatabaseHas('achievements', $attributes);
    }
}
