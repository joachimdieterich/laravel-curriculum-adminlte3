<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Curriculum;
use Facades\Tests\Setup\CurriculumFactory;

class CurriculumCRUDTest extends TestCase
{
     use RefreshDatabase;
     
     public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();
    }
    
    /** @test 
     * Use Route: GET|HEAD, curricula, curricula.index
     */
    public function an_administrator_see_curricula() 
    { 
        
        $this->get("curricula")       
             ->assertStatus(200);
        
        /* Use Datatables */
        $curriculum = Curriculum::first();
        $this->get("curricula/list")
             ->assertStatus(200)
             ->assertViewHasAll(compact($curriculum));
    }
    
    /** @test 
     * Use Route: POST, curricula, curricula.store
     */     
    public function an_administrator_create_an_curriculum()
    { 
        
        $this->post("curricula" , $attributes = factory('App\Curriculum')->raw());
        
        $this->assertDatabaseHas('curricula', $attributes);
    }
    
    /** @test 
     * Use Route: POST, curricula, curricula.index
     */     
    public function an_administrator_get_create_view_for_curricula()
    { 
        
        $this->get("curricula/create")
             ->assertStatus(200);
    }
    
    /** @test 
     * Use Route: DELETE, curricula/{curriculum}, curricula.destroy
     */  
    public function an_administrator_delete_a_curriculum()
    {
        $this->post("curricula" , $curriculum = factory('App\Curriculum')->raw());
        $id = Curriculum::where('title', $curriculum['title'])->first()->id;
        
        $this->followingRedirects()
                ->delete("curricula/". $id )
                ->assertStatus(200);
        
        $this->assertDatabaseMissing('curricula', $curriculum);
    }
    
    /** @test 
     * Use Route: GET|HEAD, curricula/{curriculum}, curricula.show
     */
    public function an_administrator_see_details_of_a_curriculum() 
    { 
        $curriculum = CurriculumFactory::create();
        //dd($curriculum->id);
        $this->get("curricula/{$curriculum->id}")       
             ->assertStatus(200)
             ->assertViewHasAll(compact($curriculum));
    }
    
    /** @test 
     * Use Route: PUT|PATCH, curricula/{curriculum}, curricula.update
     */
    public function an_administrator_update_a_curriculum()
    {
        $this->withoutExceptionHandling();
        $this->post("curricula" , $attributes = factory('App\Curriculum')->raw());
        
        $this->assertDatabaseHas('curricula', $attributes);
        
        $this->patch("curricula/". Curriculum::where('title', '=', $attributes['title'])->first()->id , $new_attributes = factory('App\Curriculum')->raw());
        
        $this->assertDatabaseHas('curricula', $new_attributes);
    }
    
    /** @test 
     * Use Route: GET|HEAD, curricula/{curriculum}/edit, curricula.edit
     */     
    public function an_administrator_get_edit_view_for_a_curriculum()
    { 
        $curriculum = CurriculumFactory::create();
        
        $this->get("curricula/{$curriculum->id}/edit")
             ->assertStatus(200)
             ->assertSessionHasAll(compact($curriculum));
    }
   
}
