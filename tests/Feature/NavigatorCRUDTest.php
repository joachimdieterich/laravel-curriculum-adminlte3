<?php

namespace Tests\Feature;

use App\Navigator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigatorCRUDTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();
    }

    /** @test
     * Use Route: GET|HEAD, navigators, navigators.index
     */
    public function an_administrator_see_navigators()
    {
        $this->get('navigators')
             ->assertStatus(200);

        /* Use Datatables */
        $navigators = Navigator::first();
        $this->get('navigators/list')
             ->assertStatus(200)
             ->assertViewHasAll(compact($navigators));
    }

    /** @test
     * Use Route: POST, navigators, navigators.index
     */
    public function an_administrator_create_a_navigator()
    {
        $attributes = factory('App\Navigator')->raw();

        $this->post('navigators', $attributes)
                ->assertStatus(302);

        $this->assertDatabaseHas('navigators', $attributes);
    }

    /** @test
     * Use Route: POST, navigators, navigators.index
     */
    public function an_administrator_get_create_view_for_a_navigator()
    {
        $this->get('navigators/create')
             ->assertStatus(200);
    }

    /** @test
     * Use Route: DELETE, navigators/{navigator}, navigators.destroy
     */
    public function an_administrator_delete_a_navigator()
    {
        $this->post('navigators', $navigator = factory('App\Navigator')->raw());
        $id = Navigator::where('title', $navigator['title'])->first()->id;

        $this->followingRedirects()
                ->delete('navigators/'.$id)
                ->assertStatus(200);

        $this->assertDatabaseMissing('navigators', $navigator);
    }

    /** @test
     * Use Route: GET|HEAD, navigators/{navigator}, navigators.show
     */
    public function an_administrator_see_details_of_an_navigator()
    {
        $this->post('navigators', $navigator = factory('App\Navigator')->raw());

        $navigator = Navigator::where('title', $navigator['title'])->first();

        $this->get("navigators/{$navigator->id}")
             ->assertStatus(200)
             ->assertViewHasAll(compact($navigator));
    }

    /** @test
     * Use Route: PUT|PATCH, navigators/{navigator}, navigators.update
     */
    public function an_administrator_update_a_navigator()
    {
        $this->withoutExceptionHandling();
        $this->post('navigators', $navigator = factory('App\Navigator')->raw());
        $navigator = Navigator::where('title', $navigator['title'])->first()->toArray();

        $this->assertDatabaseHas('navigators', $navigator);

        $this->patch('navigators/'.$navigator['id'], $new_attributes = factory('App\Navigator')->raw());
        $navigator_edit = Navigator::where('title', $new_attributes['title'])->first()->toArray();

        $this->assertDatabaseHas('navigators', $navigator_edit);
    }

    /** @test
     * Use Route: GET|HEAD, navigators/{navigator}/edit, navigators.edit
     */
    public function an_administrator_get_edit_view_for_a_navigator()
    {
        $this->post('navigators', $navigator = factory('App\Navigator')->raw());
        $navigator = Navigator::where('title', $navigator['title'])->first();
        $this->withoutExceptionHandling();
        $this->get("navigators/{$navigator->id}/edit")
             ->assertStatus(200)
             ->assertSessionHasAll(compact($navigator));
    }
}
