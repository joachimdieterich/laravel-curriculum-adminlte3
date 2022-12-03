<?php

namespace Tests\Feature;

use App\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageOrganizationTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();
    }

    /** @test */
    public function an_administrator_update_the_status_of_an_organization()
    {

        /* add new organization */
        $this->post('/organizations', $attributes = Organization::factory()->raw());

        $this->assertDatabaseHas('organizations', $attributes);

        /* edit organization */
        $attributes['status_id'] = 2;
        $attributes['state_id'] = 'DE-RP';
        $attributes['country_id'] = 'DE';

        $this->patch('/organizations/'.(Organization::where(
                        'title', '=', $attributes['title'])->first()->id),
                        [
                            'status_id' => 2,
                            'state_id' => 'DE-RP',
                            'country_id' => 'DE',
                            'organization_type_id' => $attributes['organization_type_id'],
                        ]);

        $this->assertDatabaseHas('organizations', $attributes);
    }
}
