<?php

namespace Tests\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class ApiUserDashboardTest extends TestCase {

    use RefreshDatabase;

    /** @test */
    public function an_unauthificated_client_can_not_get_users_dashboard_data() 
    {
        
        $this->get('/api/v1/users/1/dashboard')->assertStatus(302);
        $this->contains('login');
    }

    /** @test 
     * Use Route: GET, /api/v1/states
     */
    public function an_authificated_client_can_get_users_dashboard_data() 
    {

        $this->signInApiAdmin();
        // Dummy event used till fullcalendar is implemented
        $event = [
             "Event from curriculum", //event title
                false, //full day event?
                '2019-08-02 10:00:00 UTC+2', //start time, must be a DateTime object or valid DateTime format (http://bit.ly/1z7QWbg)
                '2019-08-02 12:00:00 UTC+2', //end time, must be a DateTime object or valid DateTime format (http://bit.ly/1z7QWbg),
                1, //optional event ID
        ];
        
        $this->get('/api/v1/users/1/dashboard')
                ->assertStatus(200)
                ->assertSee(json_encode([
                    'enrollments' => User::find(1)->groups()->with(['curricula'])->get(),
                    'notifications' => User::find(1)->notifications,
                    'events' => [$event],
                    ]));
    }

}
