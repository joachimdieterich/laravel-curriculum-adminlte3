<?php

namespace Tests\Api;

use App\Config;
use App\Curriculum;
use App\Metadataset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiCurriculumTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_unauthenticated_client_can_not_get_curricula()
    {
        $this->get('/api/v1/curricula')->assertStatus(302);
        $this->stringContains('login');
    }

    /** @test
     * Use Route: GET, /api/v1/groups
     */
    public function an_authenticated_client_can_get_all_curricula()
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/curricula')
             ->assertStatus(200)
             ->assertJson(Curriculum::all()->toArray());
    }

    /** @test
     * Use Route: GET, /api/v1/groups/{id}
     */
    public function an_authenticated_client_can_get_a_curricula()
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/curricula/1')
             ->assertStatus(200)
             ->assertJson(Curriculum::find(1)->toArray());
    }

    /** @test
     * Use Route: GET, /api/v1/curricula/1/metadataset?password={password}
     */
    public function an_authenticated_client_can_not_get_the_metadataset_of_a_curriculum_without_password()
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/curricula/1/metadataset?password=wrongPassword')
            ->assertStatus(420);
    }

    /** @test
     * Use Route: GET, /api/v1/curricula/1/metadataset?password={password}
     */
    public function an_authenticated_client_can_get_the_metadataset_of_a_curriculum()
    {
        $this->signInApiAdmin();

        $config = Config::create([
            'key' => 'metadata_password',
            'value' => 'password',
            'data_type' => 'string',
        ]);
        $this->get("/api/v1/curricula/1/metadataset?password={$config->value}")
            ->assertStatus(200);
        $this->stringContains('deactivated: please use /v1/curricula/metadatasets?password={password}');
    }

    /** @test
     * Use Route: GET, /api/v1/curricula/metadatasets?password={password}
     */
    public function an_authenticated_client_can_not_get_metadatasets_of_all_curricula()
    {
        $this->signInApiAdmin();

        $this->post('/metadatasets', $attributes = ['version' => 1]);

        $config = Config::create([
            'key' => 'metadata_password',
            'value' => 'password',
            'data_type' => 'string',
        ]);
        $this->get("/api/v1/curricula/metadatasets?password={$config->value}")
            ->assertSee(json_decode(htmlspecialchars(Metadataset::find(1)->metadataset)))
            ->assertStatus(200);
    }
}
