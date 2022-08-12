<?php

namespace Tests\Feature;

use App\Medium;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaCRUDTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();
    }

    /** @test
     * Use Route: GET|HEAD, media, media.index
     */
    public function an_administrator_can_get_media()
    {
        $media = Medium::all();
        $this->get('media')
             ->assertStatus(200)
             ->assertSee($media->toArray());
    }

    /** @test
     * Use Route: POST, media, media.store
     */
    /*public function an_administrator_upload_an_media()
    {

        $this->post("media" , $attributes = factory('App\Medium')->raw())
                ->assertStatus(201);

        $this->assertDatabaseHas('media', $attributes);
    }*/

    /** @test
     * Use Route: POST, roles, roles.index
     */
//    public function an_administrator_get_create_view_for_media()
//    {
//
//        $this->get("media/create")
//             ->assertStatus(200);
//    }

    /** @test
     * Use Route: DELETE, media/massDestroy media.massDestroy
     */
//    public function an_admin_can_mass_delete_media()
//    {
//
//        $media = factory(User::class, 50)->create();
//        $ids = $media->pluck('id')->toArray();
//
//        $this->delete("/media/massDestroy" , $attributes = [
//                    'ids' =>  $ids,
//                ])->assertStatus(204);
//
//        foreach($ids AS $id){
//            $this->assertSoftDeleted('media', [
//                'id' => $id
//            ]);
//        }
//    }

    /** @test
     * Use Route: DELETE, media/{user}, media.destroy
     */
//    public function an_administrator_delete_a_role()
//    {
//        $this->withoutExceptionHandling();
//        $user = UserFactory::create();
//
//        $this->followingRedirects()
//                ->delete("media/". $user->id )
//                ->assertStatus(200);
//    }

    /** @test
     * Use Route: GET|HEAD, media/{user}, media.show
     */
//    public function an_administrator_see_details_of_an_user()
//    {
//
//        $user = UserFactory::create();
//
//        $this->get("media/{$user->id}")
//             ->assertStatus(200)
//             ->assertViewHasAll(compact($user));
//    }

    /** @test
     * Use Route: PUT|PATCH, media/{user}, media.update
     */
//    public function an_administrator_update_an_user()
//    {
//        $this->post("media" , $attributes = factory('App\User')->raw());
//        $user = User::where('username', $attributes['username'])->first()->toArray();
//
//        $this->assertDatabaseHas('media', $user);
//
//        $this->patch("media/". $user['id'] , $new_attributes = factory('App\User')->raw());
//
//        $user_edit = User::where('username', $new_attributes['username'])->first()->toArray();
//        $this->assertDatabaseHas('media', $user_edit);
//    }

    /** @test
     * Use Route: GET|HEAD, media/{user}/edit, media.edit
     */
//    public function an_administrator_get_edit_view_for_media()
//    {
//        $this->post("media" , $attributes = factory('App\User')->raw());
//        $user = User::where('username', $attributes['username'])->first();
//
//        $this->get("media/{$user->id}/edit")
//             ->assertStatus(200)
//             ->assertSessionHasAll(compact($user));
//    }
}
