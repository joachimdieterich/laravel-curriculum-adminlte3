<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagePlatformTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_can_see_the_impressum()
    {
        $user = $this->signInAdmin();

        $this->get('impressum/')->assertStatus(200);
    }
}
