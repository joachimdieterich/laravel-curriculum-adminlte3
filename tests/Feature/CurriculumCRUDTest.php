<?php

namespace Tests\Feature;

use App\Curriculum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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
        $this->get('curricula')
             ->assertStatus(200);

        /* Use Datatables */
        $curricula = Curriculum::select('id', 'title')->get();
        $list = $this->get('curricula/list')
            ->assertStatus(200);
        $i = 0;
        foreach ($curricula as $curriculum) {
            if ($i === 49) {
                break;
            } //test max 50 entries (default page limit on datatables
            $list->assertJsonFragment($curriculum->toArray());
            $i++;
        }
    }

    /** @test
     * Use Route: POST, curricula, curricula.store
     */
    public function an_administrator_create_an_curriculum()
    {
        $this->followingRedirects()->post('curricula', $attributes = Curriculum::factory()->raw())
            ->assertStatus(200);

        $this->assertDatabaseHas('curricula', $attributes);
    }

    /** @test
     * Use Route: POST, curricula, curricula.index
     */
    public function an_administrator_get_create_view_for_curricula()
    {
        $this->get('curricula/create')
             ->assertStatus(200);
    }

    /** @test
     * Use Route: DELETE, curricula/{curriculum}, curricula.destroy
     */
    public function an_administrator_delete_a_curriculum()
    {
        $curriculum = Curriculum::factory()->create();

        $this->followingRedirects()
                ->delete('curricula/'.$curriculum->id)
                ->assertStatus(200);

        $this->assertDatabaseMissing('curricula', $curriculum->toArray());
    }

    /** @test
     * Use Route: GET|HEAD, curricula/{curriculum}, curricula.show
     */
    public function an_administrator_see_details_of_a_curriculum()
    {
        $curriculum = Curriculum::factory()->create();

        $this->get("curricula/{$curriculum->id}")
             ->assertStatus(200);
        //->assertSee($curriculum);
    }

    /** @test
     * Use Route: PUT|PATCH, curricula/{curriculum}, curricula.update
     */
    public function an_administrator_update_a_curriculum()
    {
        $this->post('curricula', $attributes = Curriculum::factory()->raw());
        $curriculum = Curriculum::where('title', $attributes['title'])->first()->toArray();

        $this->assertDatabaseHas('curricula', $curriculum);

        $this->patch('curricula/'.$curriculum['id'], $new_attributes = Curriculum::factory()->raw());

        $curriculum_edit = Curriculum::where('title', $new_attributes['title'])->first()->toArray();
        $this->assertDatabaseHas('curricula', $curriculum_edit);
    }

    /** @test
     * Use Route: GET|HEAD, curricula/{curriculum}/edit, curricula.edit
     */
    public function an_administrator_get_edit_view_for_a_curriculum()
    {
        $this->post('curricula', $attributes = Curriculum::factory()->raw());
        $curriculum = Curriculum::where('title', $attributes['title'])->first();

        $this->get("curricula/{$curriculum->id}/edit")
            ->assertStatus(200)
            ->assertSee([
                'title' => $curriculum->title,
            ]);
    }
}
