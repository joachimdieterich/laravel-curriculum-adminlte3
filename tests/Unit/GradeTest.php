<?php

namespace Tests\Unit;

use App\Grade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_a_title()
    {
        $grade = Grade::findOrFail(1);

        $this->assertTrue(is_string($grade->title));
    }
}
