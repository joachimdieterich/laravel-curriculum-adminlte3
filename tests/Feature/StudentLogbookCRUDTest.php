<?php

namespace Tests\Feature;

use App\Config;
use App\Logbook;
use Facades\Tests\Setup\LogbookFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentLogbookCRUDTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInRole('student');
    }

    /** @test
     * Use Route: GET|HEAD, logbooks, logbooks.index
     */
    public function a_student_see_logbooks()
    {
        $this->get('logbooks')
            ->assertStatus(200);

        /* Use Datatables */
        $logbook = Logbook::first();
        $this->get('logbooks/list')
            ->assertStatus(200)
            ->assertViewHasAll(compact($logbook));
    }

    /** @test
     * Use Route: POST, logbooks, logbooks.store
     */
    public function a_student_create_an_Logbook()
    {
        $this->post('logbooks', $attributes = factory('App\Logbook')->raw());

        $this->assertDatabaseHas('logbooks', $attributes);
    }

    /** @test
     * Use Route: GET, logbooks, logbooks.create
     */
    public function a_student_get_create_view_for_logbooks()
    {
        $this->get('logbooks/create')
            ->assertStatus(200);
    }

    /** @test
     * Use Route: GET, logbooks, logbooks.create
     */
    public function a_student_cannot_get_create_view_for_logbooks_if_limiter_is_reached()
    {
        $this->get('logbooks/create')
            ->assertStatus(200); //limit not reached

        $logbook = LogbookFactory::create(); //create first logbook

        Config::create([
            'key' => 'logbook_limiter',
            'value' => 1,
            'referenceable_type' => 'App\\Role',
            'referenceable_id' => 6,
            'data_type' => 'integer',
        ]); // define limit = 1

        $this->get('logbooks/create')
            ->assertStatus(402); // limit reached
    }

    /** @test
     * Use Route: DELETE, logbooks/{Logbook}, logbooks.destroy
     */
    public function a_student_cannot_delete_a_Logbook()
    {
        $logbook = LogbookFactory::create();

        $this->followingRedirects()
            ->delete('logbooks/'.$logbook->id)
            ->assertStatus(403);

        $this->assertDatabaseHas('logbooks', $logbook->toArray());
    }

    /** @test
     * Use Route: GET|HEAD, logbooks/{Logbook}, logbooks.show
     */
    public function a_student_see_details_of_a_Logbook()
    {
        $logbook = LogbookFactory::create();

        //dd($logbook->id);
        $this->get("logbooks/{$logbook->id}")
            ->assertStatus(200)
            ->assertViewHasAll(compact($logbook));
    }

    /** @test
     * Use Route: GET|HEAD, logbooks/{Logbook}, logbooks.show
     */
    public function a_student_cannot_see_details_of_a_foreign_Logbook()
    {
        $logbook = LogbookFactory::create();

        $logbook->owner_id = 1;
        $logbook->save();
        $this->get("logbooks/{$logbook->id}")
            ->assertStatus(403);
    }

    /** @test
     * Use Route: PUT|PATCH, logbooks/{Logbook}, logbooks.update
     */
    public function a_student_update_a_Logbook()
    {
        $this->post('logbooks', $attributes = factory('App\Logbook')->raw());

        $this->assertDatabaseHas('logbooks', $attributes);

        $this->patch('logbooks/'.Logbook::where('title', '=', $attributes['title'])->first()->id, $new_attributes = factory('App\Logbook')->raw());

        $this->assertDatabaseHas('logbooks', $new_attributes);
    }

    /** @test
     * Use Route: PUT|PATCH, logbooks/{Logbook}, logbooks.update
     */
    public function a_student_cannot_update_a_foreign_Logbook()
    {
        $logbook = LogbookFactory::create();

        $logbook->owner_id = 1;
        $logbook->save();

        $this->patch("logbooks/{$logbook->id}", $new_attributes = factory('App\Logbook')->raw())
            ->assertStatus(403);
    }

    /** @test
     * Use Route: GET|HEAD, logbooks/{Logbook}/edit, logbooks.edit
     */
    public function a_student_get_edit_view_for_a_Logbook()
    {
        $logbook = LogbookFactory::create();

        $this->get("logbooks/{$logbook->id}/edit")
            ->assertStatus(200)
            ->assertSessionHasAll(compact($logbook));
    }

    /** @test
     * Use Route: GET|HEAD, logbooks/{Logbook}/edit, logbooks.edit
     */
    public function a_student_cannot_get_edit_view_for_a_foreign_Logbook()
    {
        $logbook = LogbookFactory::create();

        $logbook->owner_id = 1;
        $logbook->save();

        $this->get("logbooks/{$logbook->id}/edit")
            ->assertStatus(403);
    }
}
