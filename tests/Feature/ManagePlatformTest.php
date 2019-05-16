<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Context;
use App\Content;
use App\ContentSubscription;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Facades\Tests\Setup\ContentFactory;
use Facades\Tests\Setup\RoleFactory;
use Illuminate\Database\QueryException;


class ManagePlatformTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function an_admin_can_see_the_impressum()
    {
        $this->withoutExceptionHandling();
        $user = $this->signInAdmin();
        
        $this->get('impressum/')->assertStatus(200);
        
       
    }
    
}
