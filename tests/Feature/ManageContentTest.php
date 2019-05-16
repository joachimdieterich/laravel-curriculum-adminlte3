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


class ManageContentTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function an_authentificated_can_create_a_content()
    {
        //$this->withoutExceptionHandling();
        $user = $this->signIn();
        
        $this->get('contents/create')->assertStatus(200);
        
        $this->followingRedirects()->post("/contents" , $attributes = [
                    'title' => 'My first content',
                    'content' => 'Hello World!', 
                    'owner_id' => $user->id, 
                ])
                ->assertStatus(200);
        $this->assertDatabaseHas('contents', $attributes);
    }
    
}
