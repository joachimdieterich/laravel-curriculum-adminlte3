<?php

namespace Tests\Unit;

use App\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CountryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_states()
    {
        $country = Country::findOrFail('DE');
        $this->assertTrue($country->states()->first()->country == 'DE');
    }
}
