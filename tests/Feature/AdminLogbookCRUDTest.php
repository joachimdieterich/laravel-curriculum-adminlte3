<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Logbook;
use Facades\Tests\Setup\LogbookFactory;

class AdminLogbookCRUDTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInRole('admin');
    }

    /** @test
     * Use Route: GET|HEAD, logbooks, logbooks.index
     */
    public function an_administrator_see_logbooks()
    {
        $this->get("logbooks")
            ->assertStatus(200);

        /* Use Datatables */
        $Logbook = Logbook::first();
        $this->get("logbooks/list")
            ->assertStatus(200)
            ->assertViewHasAll(compact($Logbook));
    }

    /** @test
     * Use Route: POST, logbooks, logbooks.store
     */
    public function an_administrator_create_an_Logbook()
    {
        $this->post("logbooks", $attributes = factory('App\Logbook')->raw());

        $this->assertDatabaseHas('logbooks', $attributes);
    }

    /** @test
     * Use Route: POST, logbooks, logbooks.index
     */
    public function an_administrator_get_create_view_for_logbooks()
    {
        $this->get("logbooks/create")
            ->assertStatus(200);
    }

    /** @test
     * Use Route: DELETE, logbooks/{Logbook}, logbooks.destroy
     */
    public function an_administrator_delete_a_Logbook()
    {
        $Logbook = LogbookFactory::create();

        $this->followingRedirects()
            ->delete("logbooks/" . $Logbook->id)
            ->assertStatus(200);

        $this->assertDatabaseMissing('logbooks', $Logbook->toArray());
    }

    /** @test
     * Use Route: GET|HEAD, logbooks/{Logbook}, logbooks.show
     */
    public function an_administrator_see_details_of_a_Logbook()
    {
        $Logbook = LogbookFactory::create();

        //dd($Logbook->id);
        $this->get("logbooks/{$Logbook->id}")
            ->assertStatus(200)
            ->assertViewHasAll(compact($Logbook));
    }

    /** @test
     * Use Route: PUT|PATCH, logbooks/{Logbook}, logbooks.update
     */
    public function an_administrator_update_a_Logbook()
    {
        $this->withoutExceptionHandling();

        $this->post("logbooks", $attributes = factory('App\Logbook')->raw());

        $this->assertDatabaseHas('logbooks', $attributes);

        $this->patch("logbooks/" . Logbook::where('title', '=', $attributes['title'])->first()->id, $new_attributes = factory('App\Logbook')->raw());

        $this->assertDatabaseHas('logbooks', $new_attributes);
    }

    /** @test
     * Use Route: GET|HEAD, logbooks/{Logbook}/edit, logbooks.edit
     */
    public function an_administrator_get_edit_view_for_a_Logbook()
    {
        $Logbook = LogbookFactory::create();

        $this->get("logbooks/{$Logbook->id}/edit")
            ->assertStatus(200)
            ->assertSessionHasAll(compact($Logbook));
    }

}
