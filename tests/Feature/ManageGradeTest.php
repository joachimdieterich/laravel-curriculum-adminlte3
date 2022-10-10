<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageGradeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_administrator_see_grades()
    {
        $this->signInAdmin();

        $this->followingRedirects()->get('grades')
            ->assertStatus(200);
    }
}
