<?php

namespace Tests\Feature;

use App\Certificate;
use Facades\Tests\Setup\CertificateFactory;
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
        $certificate = Certificate::first();
        $this->get('certificates/list')
             ->assertStatus(200)
             ->assertViewHasAll(compact($certificate));
    }

    /** @test
     * Use Route: POST, certificates, certificates.store
     */
    public function an_administrator_create_an_certificate_template()
    {
        $this->post('certificates', $attributes = factory('App\Certificate')->raw());

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
        $this->post('certificates', $certificate = factory('App\Certificate')->raw());
        $id = Certificate::where('title', $certificate['title'])->first()->id;

        $this->followingRedirects()
                ->delete('certificates/'.$id)
                ->assertStatus(200);

        $this->assertDatabaseMissing('certificates', $certificate);
    }

    /** @test
     * Use Route: GET|HEAD, certificates/{certificate}, certificates.show
     */
    public function an_administrator_see_details_of_a_certificate_template()
    {
        $certificate = CertificateFactory::create();
        //dd($certificate->id);
        $this->get("certificates/{$certificate->id}")
             ->assertStatus(200)
             ->assertViewHasAll(compact($certificate));
    }

    /** @test
     * Use Route: PUT|PATCH, certificates/{certificate}, certificates.update
     */
    public function an_administrator_update_a_certificate_template()
    {
        $this->withoutExceptionHandling();
        $this->post('certificates', $attributes = factory('App\Certificate')->raw());

        $this->assertDatabaseHas('certificates', $attributes);

        $this->patch('certificates/'.Certificate::where('title', '=', $attributes['title'])->first()->id, $new_attributes = factory('App\Certificate')->raw());

        $this->assertDatabaseHas('certificates', $new_attributes);
    }

    /** @test
     * Use Route: GET|HEAD, certificates/{certificate}/edit, certificates.edit
     */
    public function an_administrator_get_edit_view_for_a_certificate_template()
    {
        $certificate = CertificateFactory::create();

        $this->get("certificates/{$certificate->id}/edit")
             ->assertStatus(200)
             ->assertSessionHasAll(compact($certificate));
    }

    /** @test
     * Use Route: POST, certificates, certificates.generate
     */
    public function an_administrator_create_an_certificate_based_on_an_existing_template()
    {
//
//        $certificate = CertificateFactory::create();
//
//        $this->post("certificates/generate",
//                    $attributes = [
//                        'id' => $certificate->id,
//                    ]);
    }
}
