<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Grade;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GradeTest extends TestCase
{
    use RefreshDatabase;
    
    
    /** @test */
    public function it_has_a_title() {
        
        $grade = Grade::findOrFail(1); 
        
        $this->assertTrue(is_string($grade->title));
    }
    
}
