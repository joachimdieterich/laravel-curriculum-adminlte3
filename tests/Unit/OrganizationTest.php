<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Organization;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrganizationTest extends TestCase
{
    use RefreshDatabase;
    
    
    /** @test */
    public function it_has_a_state() {
        $this->seeder();
        $org = Organization::first(); 
       
        $this->assertInstanceOf('App\State', $org->state()->first());
        
    }
    /** @test */
    public function it_has_a_country() {
        $this->seeder();
        $org = Organization::first(); 
        //dd($org->country()->first());
        $this->assertInstanceOf('App\Country', $org->country()->first());
        
    }
    
}
