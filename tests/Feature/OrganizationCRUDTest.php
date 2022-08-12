<?php

namespace Tests\Feature;

use App\Organization;
use Facades\Tests\Setup\OrganizationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationCRUDTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();
    }

    /** @test
     * Use Route: GET|HEAD, organizations, organizations.index
     */
    public function an_administrator_see_organizations()
    {
        $this->get('organizations')
             ->assertStatus(200);

        /* Use Datatables */
        $organizations = Organization::select('title', 'street', 'postcode', 'city')->get();
        $list = $this->get('organizations/list')
            ->assertStatus(200);
        $i = 0;
        foreach ($organizations as $organization)
        {
            if ($i === 49) { break; } //test max 50 user (default page limit on datatables
            $list->assertJsonFragment($organization->toArray());
            $i++;
        }
    }

    /** @test
     * Use Route: POST, organizations, organizations.store
     */
    public function an_administrator_create_an_organization()
    {
        $this->followingRedirects()->post('organizations', $attributes = Organization::factory()->raw())
            ->assertStatus(200);

        $this->assertDatabaseHas('organizations', $attributes);
    }

    /** @test
     * Use Route: POST, organizations, organizations.create
     */
    public function an_administrator_get_create_view_for_organization()
    {
        $this->get('organizations/create')
             ->assertStatus(200);
    }

    /** @test
     * Use Route: DELETE, organizations/massDestroy organizations.massDestroy
     */
    public function an_administrator_can_mass_delete_organizations()
    {
        $orgs = Organization::factory(50)->create();
        $ids = $orgs->pluck('id')->toArray();

        $this->delete('/organizations/massDestroy', $attributes = [
            'ids' =>  $ids,
        ])->assertStatus(204);

        foreach ($ids as $id) {
            $this->assertDatabaseMissing('organizations', [
                'id' => $id,
            ]);
        }
    }

    /** @test
     * Use Route: DELETE, organizations/{organization}, organizations.destroy
     */
    public function an_administrator_delete_an_organization()
    {

        /* add new organization */
        $org = Organization::factory()->create();

        $this->followingRedirects()
                ->delete('organizations/'.$org->id)
                ->assertStatus(200);
    }

    /** @test
     * Use Route: GET|HEAD, organizations/{organization}, organizations.show
     */
    public function an_administrator_see_details_of_an_organizations()
    {
        $org = Organization::factory()->create();
        //dump($org);
        $this->get("organizations/{$org->id}")
             ->assertStatus(200)
             ->assertSee([
                 'title' => $org->title,
                 'street' => $org->street,
                 'postcode' => $org->postcode,
                 'city' => $org->city,
                 'phone' => $org->phone,
                 'email' => $org->email,
             ]);
    }

    /** @test
     * Use Route: PUT|PATCH, organizations/{organization}, organizations.update
     */
    public function an_administrator_update_an_organization()
    {
        $this->post('organizations', $attributes = Organization::factory()->raw());
        $attributes = Organization::where('title', $attributes['title'])->first()->toArray();

        $this->assertDatabaseHas('organizations', $attributes);
        /* edit organization*/
        $this->patch('organizations/'.Organization::where('title', '=', $attributes['title'])->first()->id, $new_attributes = Organization::factory()->raw());

        $user_edit = Organization::where('title', $new_attributes['title'])->first()->toArray();
        $this->assertDatabaseHas('organizations', $new_attributes);
    }

    /** @test
     * Use Route: GET|HEAD, organizations/{organization}/edit, organizations.edit
     */
    public function an_administrator_get_edit_view_for_organization()
    {
        $this->post('organizations', $attributes = Organization::factory()->raw());
        $org = Organization::where('title', $attributes['title'])->first();

        $this->get("organizations/{$org->id}/edit")
             ->assertStatus(200)
             ->assertSee([
                'title' => $org->title,
                'street' => $org->street,
                'postcode' => $org->postcode,
                'city' => $org->city,
                'phone' => $org->phone,
                'email' => $org->email,
             ]);
    }
}
