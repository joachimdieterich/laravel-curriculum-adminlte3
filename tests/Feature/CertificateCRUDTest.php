<?php

namespace Tests\Feature;

use App\Certificate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateCRUDTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();
    }

    /** @test
     * Use Route: GET|HEAD, certificates, certificates.index
     */
    public function an_administrator_see_certificate_templates()
    {
        $this->get('certificates')
             ->assertStatus(200);

        /* Use Datatables */
        $certificates = Certificate::select('id', 'title')->get();
        $list = $this->get('certificates/list')
            ->assertStatus(200);
        $i = 0;
        foreach ($certificates as $certificate) {
            if ($i === 49) {
                break;
            } //test max 50 entries (default page limit on datatables
            $list->assertJsonFragment($certificate->toArray());
            $i++;
        }
    }

    /** @test
     * Use Route: POST, certificates, certificates.store
     */
    public function an_administrator_create_an_certificate_template()
    {
        $this->followingRedirects()->post('certificates', $attributes = Certificate::factory()->raw())
            ->assertStatus(200);

        $this->assertDatabaseHas('certificates', $attributes);
    }

    /** @test
     * Use Route: POST, certificates, certificates.index
     */
    public function an_administrator_get_create_view_for_certificate_templates()
    {
        $this->get('certificates/create')
             ->assertStatus(200);
    }

    /** @test
     * Use Route: DELETE, certificates/{certificate}, certificates.destroy
     */
    public function an_administrator_delete_a_certificate_template()
    {
        $certificate = Certificate::factory()->create();

        $this->followingRedirects()
            ->delete('certificates/'.$certificate->id)
            ->assertStatus(200);
    }

    /** @test
     * Use Route: GET|HEAD, certificates/{certificate}, certificates.show
     */
    public function an_administrator_see_details_of_a_certificate_template()
    {
        $certificate = Certificate::factory()->create();

        $this->get("certificates/{$certificate->id}")
            ->assertStatus(200);
        //->assertSee($certificate->toArray());
    }

    /** @test
     * Use Route: PUT|PATCH, certificates/{certificate}, certificates.update
     */
    public function an_administrator_update_a_certificate_template()
    {
        $this->post('certificates', $attributes = Certificate::factory()->raw());
        $certificate = Certificate::where('title', $attributes['title'])->first()->toArray();

        $this->assertDatabaseHas('certificates', $certificate);

        $this->patch('certificates/'.$certificate['id'], $new_attributes = Certificate::factory()->raw());

        $certificate_edit = Certificate::where('title', $new_attributes['title'])->first()->toArray();
        $this->assertDatabaseHas('certificates', $certificate_edit);
    }

    /** @test
     * Use Route: GET|HEAD, certificates/{certificate}/edit, certificates.edit
     */
    public function an_administrator_get_edit_view_for_a_certificate_template()
    {
        $this->post('certificates', $attributes = Certificate::factory()->raw());
        $certificate = Certificate::where('title', $attributes['title'])->first();

        $this->get("certificates/{$certificate->id}/edit")
            ->assertStatus(200)
            ->assertSee([
                'title' => $certificate->title,
                'description' => $certificate->description,
                'body' => $certificate->body,
            ]);
    }

    /** @test
     * Use Route: POST, certificates, certificates.generate
     */
    public function an_administrator_create_an_certificate_based_on_an_existing_template()
    {
    }
}
