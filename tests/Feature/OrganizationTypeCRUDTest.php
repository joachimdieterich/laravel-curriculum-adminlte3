<?php

namespace Tests\Feature;

use App\OrganizationType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationTypeCRUDTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();
    }

    /** @test
     * Use Route: GET|HEAD, organizationtypes, organizationtypes.index
     */
    public function an_administrator_see_organization_types()
    {
        $this->get('organizationtypes')
             ->assertStatus(200);

        /* Use Datatables */
        $organizationTypes = OrganizationType::select('id', 'title')->get();
        $list = $this->get('organizationtypes/list')
            ->assertStatus(200);
        $i = 0;
        foreach ($organizationTypes as $organizationType) {
            if ($i === 49) {
                break;
            } //test max 50 entries (default page limit on datatables
            $list->assertJsonFragment($organizationType->toArray());
            $i++;
        }
    }

    /** @test
     * Use Route: POST, organizationtypes, organizationtypes.index
     */
    public function an_administrator_create_an_organization_type()
    {
        $this->followingRedirects()
            ->post('organizationtypes', $attributes = OrganizationType::factory()->raw())
            ->assertStatus(200);

        $this->assertDatabaseHas('organization_types', $attributes);
    }

    /** @test
     * Use Route: POST, organizationtypes, organizationTypes.create
     */
    public function an_administrator_get_create_view_for_organization()
    {
        $this->get('organizationtypes/create')
             ->assertStatus(200);
    }

    /** @test
     * Use Route: DELETE, organizationtypes/{organizationType}, organizationtypes.destroy
     */
    public function an_administrator_delete_an_organization_type()
    {
        $org_type = OrganizationType::factory()->create();

        $this->followingRedirects()
                ->delete('organizationtypes/'.$org_type->id)
                ->assertStatus(200);

        $this->assertDatabaseMissing('organization_types', $org_type->toArray());
    }

    /** @test
     * Use Route: GET|HEAD, organizationtypes/{organizationtype}, organizationtypes.show
     */
    public function an_administrator_see_details_of_an_organization_types()
    {
        $org_type = OrganizationType::factory()->create();

        $this->get("organizationtypes/{$org_type->id}")
             ->assertStatus(200)
             ->assertSee($org_type->toArray());
    }

    /** @test
     * Use Route: PUT|PATCH, organizationtypes/{organizationtype}, organizationtypes.update
     */
    public function an_administrator_update_an_organization_types()
    {
        $this->post('organizationtypes', $attributes = OrganizationType::factory()->raw());
        $organizationType = OrganizationType::where('title', $attributes['title'])->first()->toArray();

        $this->assertDatabaseHas('organization_types', $organizationType);

        $this->patch('organizationtypes/'.$organizationType['id'], $new_attributes = OrganizationType::factory()->raw());

        $organizationType_edit = OrganizationType::where('title', $new_attributes['title'])->first()->toArray();
        $this->assertDatabaseHas('organization_types', $organizationType_edit);
    }

    /** @test
     * Use Route: GET|HEAD, organizationtypes/{organizationtype}/edit, organizationtypes.edit
     */
    public function an_administrator_get_edit_view_for_organization_type()
    {
        $this->post('organizationtypes', $attributes = OrganizationType::factory()->raw());
        $organizationType = OrganizationType::where('title', $attributes['title'])->first();

        $this->get("organizationtypes/{$organizationType->id}/edit")
            ->assertStatus(200)
            ->assertSee([
                'title' => $organizationType->title,
            ]);
    }
}
