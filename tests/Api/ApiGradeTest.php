<?php

namespace Tests\Api;

use App\Grade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiGradeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_client_can_not_get_grades()
    {
        $this->get('/api/v1/grades')->assertStatus(302);
        $this->stringContains('login');
    }

    /** @test
     * Use Route: GET, /api/v1/grades
     */
    public function an_authenticated_client_can_get_all_grades()
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/grades')
                ->assertStatus(200);
    }

    /** @test
     * Use Route: GET, /api/v1/grades/{id}
     */
    public function an_authenticated_client_can_get_a_grade()
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/grades/1')
                ->assertStatus(200)
                ->assertJson(Grade::find(1)->toArray());
    }
}
