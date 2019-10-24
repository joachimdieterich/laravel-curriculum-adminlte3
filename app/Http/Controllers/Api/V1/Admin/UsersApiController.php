<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\User;
use App\Notifications\Welcome;

class UsersApiController extends Controller
{
    public function index()
    {
        $users = User::all();

        return $users;
    }

    public function store()
    {
        if ($user = User::create($this->filteredRequest()))
        {
            $user->notify(new Welcome());
         
        }
        return $user;
    }

    public function update(User $user) {
        if ($user->update($this->filteredRequest())) 
        {  
            return $user->fresh();
        }
    }

    public function show(User $user)
    {
        return $user;
    }

    public function destroy(User $user)
    {
        if ($user->delete()) 
        {
            return ['message' => 'Successful deleted'];
        }
    }
    
    public function withGroups(User $user)
    {
        return  $user->where('id', $user->id)->with('groups')->get();
    }
    
    public function withOrganizations(User $user)
    {
        return  $user->where('id', $user->id)->with('organizations')->get();
    }
    
    public function withRoles(User $user)
    {
        return  $user->where('id', $user->id)->with('roles')->get();
    }
   
    
    public function dashboard(User $user)
    {
        //Dummy fullcalendar event 
        $event = [
            "Event from curriculum", //event title
                false, //full day event?
                '2019-08-02 10:00:00 UTC+2', //start time, must be a DateTime object or valid DateTime format (http://bit.ly/1z7QWbg)
                '2019-08-02 12:00:00 UTC+2', //end time, must be a DateTime object or valid DateTime format (http://bit.ly/1z7QWbg),
                1 //optional event ID
        ];
      
        return ['enrollments' => $user->groups()->with(['curricula'])->get(), 
                'notifications' => $user->notifications,
                'events' => [$event] 
               ];
    }
    
    protected function filteredRequest() {
        return array_filter(request()->all()); //filter to ignore fields with null values
    }
}
