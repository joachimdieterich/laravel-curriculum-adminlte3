<?php

namespace Tests\Unit;

use App\OrganizationType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationTypeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_a_title()
    {
        $organization_type = OrganizationType::findOrFail(1);

        $this->assertTrue(is_string($organization_type->title));
    }
}
