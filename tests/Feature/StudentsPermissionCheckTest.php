<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudentsPermissionCheckTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInStudent();
    }

    /** @test */
    public function can_access_dashboard()
    {
        $response = $this->get('/');

        $response->assertStatus(302);
    }

    /** @test */
    public function can_not_access_curriculum_management()
    {
        $response = $this->get('/curricula');
        $response->assertStatus(403);
    }

    /** @test */
    public function can_access_a_curriculum_where_its_group_is_enroled()
    {
        $this->followingRedirects()
            ->get('/courses/1')
            ->assertStatus(200); //curriculum was created by seeder
    }

    /** @test */
    public function can_not_access_a_curriculum_where_its_group_is_not_enroled()
    {
        //ID 6 == Student
        DB::table('group_user')->where('user_id', '=', 6)->delete(); //delete seeded enrolment
        DB::table('group_user')->where('user_id', '=', 8)->delete(); //delete seeded guest enrolment

        $this->get('/curricula/1')->assertStatus(403); //curriculum was created by seeder
    }

    /** @test */
    public function can_not_access_a_non_existing_curriculum()
    {
        $response = $this->get('/curricula/100'); //non-existing curriculum

        $response->assertStatus(404);
    }
}
