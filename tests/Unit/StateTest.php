<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\State;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StateTest extends TestCase
{
    use RefreshDatabase;
    
     /** @test */
    public function it_has_a_country() {
        $this->seeder();
        $state = State::findOrFail(11); //country_code == 'DE'
        
        $this->assertInstanceOf('App\Country', $state->country()->first());
    }
    
}
