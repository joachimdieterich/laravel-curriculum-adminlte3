<?php

namespace Tests\Unit;

use App\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_a_title()
    {
        $subject = Subject::findOrFail(1);

        $this->assertTrue(is_string($subject->title));
    }
}
