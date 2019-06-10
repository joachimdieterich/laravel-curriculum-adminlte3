<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Subject;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubjectTest extends TestCase
{
    use RefreshDatabase;
    
    
    /** @test */
    public function it_has_a_title() {
        
        $subject = Subject::findOrFail(1); 
        
        $this->assertTrue(is_string($subject->title));
    }
    
}
