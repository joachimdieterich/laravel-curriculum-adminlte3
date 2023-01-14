<?php

namespace Tests\Feature;

use App\Absence;
use App\Logbook;
use App\LogbookEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class AbsenceCRUDTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();
    }

    /** @test
     * Use Route: GET, absences
     */
    public function users_getting_403_on_index()
    {
        $this->get('absences')
            ->assertStatus(403);
    }

    /** @test
     * Use Route: POST, absences, absences.store
     */
    public function an_user_with_permission_absence_create_can_create_an_absence_entry()
    {
        $this->assertTrue(Gate::allows('absence_create'));

        $logbook = Logbook::Create([
            'title' => 'Test-Logbook',
            'description' => 'My test-Logbook',
            'owner_id' => auth()->user()->id,
        ]);
        $entry = LogbookEntry::firstOrCreate([
            'logbook_id' => $logbook->id,
            'title' => 'First entry',
            'description' => 'First entry description',
            'begin' => '2020-09-13 16:17:14',
            'end' => '2020-09-13 19:17:14',
            'owner_id' => auth()->user()->id,
        ]);
        $response = $this->postJson('absences', $attributes = [
            'referenceable_type' => 'App\LogbookEntry',
            'referenceable_id' => $entry->id,
            'absent_user_ids' => 6,
            'reason' => 'vacation',
            'done' => 0,
            'time' => 0,
            'owner_id' => auth()->user()->id,
        ]);
        $absence = Absence::where('reason', 'vacation')->first();
        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => $absence->toArray(),
            ]);

        $this->assertDatabaseHas('absences', $absence->toArray());
    }

    /** @test
     * Use Route: GET, absences
     */
    public function users_getting_403_on_show()
    {
        $absence = Absence::create([
            'referenceable_type' => 'App\User',
            'referenceable_id' => 1,
            'absent_user_id' => 6,
            'reason' => 'vacation',
            'done' => 0,
            'time' => 0,
            'owner_id' => auth()->user()->id,
        ]);
        $this->get(route('absences.show', $absence->id))
            ->assertStatus(403);
    }

    /** @test
     * Use Route: POST, absences, absences.update
     */
    public function an_user_with_permission_absence_edit_can_update_an_absence_entry()
    {
        //todo: create Logbook/LogbookEntry to get a valid reference
        /*$this->assertTrue(Gate::allows('absence_edit'));

        $absence = Absence::create([
            'referenceable_type' => 'App\LogbookEntry',
            'referenceable_id'   => 1,
            'absent_user_id'     => 6,
            'reason'             => 'vacation',
            'done'               => 0,
            'time'               => 0,
            'owner_id'           => auth()->user()->id
        ]);
        $response = $this->patchJson("absences/".$absence->id , [
            'done'               => 1,
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'done' => 1,
            ]);

        $absence = Absence::where('reason', 'vacation')->first();

        $this->assertEquals(1, $absence->done);*/
    }

    /** @test
     * Use Route: DELETE, absences, absences.delete
     */
    public function an_user_with_permission_absence_delete_can_destroy_an_absence_entry()
    {
        //todo: create Logbook/LogbookEntry to get a valid reference
        /*$this->assertTrue(Gate::allows('absence_delete'));

        Absence::create([
            'referenceable_type' => 'App\LogbookEntry',
            'referenceable_id'   => 1,
            'absent_user_id'     => 6,
            'reason'             => 'vacation',
            'done'               => 0,
            'time'               => 0,
            'owner_id'           => auth()->user()->id
        ]);

        $absence = Absence::where('reason', 'vacation')->first();

        $response = $this->deleteJson("absences/".$absence->id);
        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => true,
            ]);

        $this->assertDatabaseMissing('absences', $absence->toArray());*/
    }
}
