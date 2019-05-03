<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Country;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CountryTest extends TestCase
{
    use RefreshDatabase;
    
    
     /** @test */
    public function it_has_states() {
        $this->seeder();
        $country = Country::findOrFail(56); //code == 'DE'
        $this->assertTrue($country->states()->first()->country_code == 'DE');
    }
    
}
