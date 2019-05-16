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
        
        $this->assertInstanceOf('App\Country', $org->country()->first());
        
    }
   
    /** @test */
    public function it_has_a_organization_type() {
        $this->seeder();
        $org = Organization::first(); 
       
        $this->assertInstanceOf('App\OrganizationType', $org->type()->first());
        
    }
    
    /** @test */
    public function it_has_a_status() {
        $this->seeder();
        $org = Organization::first(); 
       
        $this->assertInstanceOf('App\Status', $org->status()->first());
        
    }
    
}
