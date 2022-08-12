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
        $navigators = Navigator::select('id', 'title')->get();
        $list = $this->get('navigators/list')
            ->assertStatus(200);
        $i = 0;
        foreach ($navigators as $navigator)
        {
            if ($i === 49) { break; } //test max 50 entries (default page limit on datatables
            $list->assertJsonFragment($navigator->toArray());
            $i++;
        }
    }

    /** @test
     * Use Route: POST, navigators, navigators.index
     */
    public function an_administrator_create_a_navigator()
    {
        $this->followingRedirects()
            ->post('navigators', $attributes = Navigator::factory()->raw())
            ->assertStatus(200);

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
        $navigator = Navigator::factory()->create();

        $this->followingRedirects()
            ->delete('navigators/'.$navigator->id)
            ->assertStatus(200);

        $this->assertDatabaseMissing('navigators', $navigator->toArray());
    }

    /** @test
     * Use Route: GET|HEAD, navigators/{navigator}, navigators.show
     */
    public function an_administrator_see_details_of_an_navigator()
    {
        $navigator = Navigator::factory()->create();

        $this->get("navigators/{$navigator->id}")
            ->assertStatus(200)
            ->assertSee([
                $navigator->title
            ]);
    }

    /** @test
     * Use Route: PUT|PATCH, navigators/{navigator}, navigators.update
     */
    public function an_administrator_update_a_navigator()
    {
        $this->post('navigators', $attributes = Navigator::factory()->raw());
        $navigator = Navigator::where('title', $attributes['title'])->first()->toArray();

        $this->assertDatabaseHas('navigators', $navigator);

        $this->patch('navigators/'.$navigator['id'], $new_attributes = Navigator::factory()->raw());

        $navigator_edit = Navigator::where('title', $new_attributes['title'])->first()->toArray();
        $this->assertDatabaseHas('navigators', $navigator_edit);
    }

    /** @test
     * Use Route: GET|HEAD, navigators/{navigator}/edit, navigators.edit
     */
    public function an_administrator_get_edit_view_for_a_navigator()
    {
        $this->post('navigators', $attributes = Navigator::factory()->raw());
        $navigator = Navigator::where('title', $attributes['title'])->first();

        $this->get("navigators/{$navigator->id}/edit")
            ->assertStatus(200)
            ->assertSee([
                'title' => $navigator->title,
            ]);
    }
}
