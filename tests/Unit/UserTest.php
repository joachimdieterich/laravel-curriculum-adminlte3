<?php

namespace Tests\Unit;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function has_a_group()
    {
        $user = User::findOrFail(1);

        $this->assertInstanceOf('App\Group', $user->groups()->first());
    }

//    /** @test */
//    public function has_a_current_period_id() {
//
//        $user = User::findOrFail(1);
//
//    }
}
