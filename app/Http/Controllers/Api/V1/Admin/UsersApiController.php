<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Notifications\Welcome;
use App\User;

class UsersApiController extends Controller
{
    public function index()
    {
        if (request()->common_name)
        {
            return  User::where('common_name', request()->common_name)->get();
        }
        else
        {
            $users = User::all();

            return $users;
        }

    }

    public function store()
    {
        //tempfix for false provisioned users
        if (User::withTrashed()->where('username', request()->username)->exists()) {
            $user = User::withTrashed()->where('username', request()->username)->get()->first();
            $user->update(['username' => 'alt_'.request()->username]);
        }
        if (User::withTrashed()->where('email', request()->email)->exists()) {
            $user = User::withTrashed()->where('email', request()->email)->get()->first();
            $user->update(['email' => 'alt_'.request()->email]);
        }

        if (User::withTrashed()->where('common_name', request()->common_name)->exists()) {
            User::withTrashed()->where('common_name', request()->common_name)->restore();
            $user = User::where('common_name', request()->common_name)->get()->first();
            $user->update($this->filteredRequest());
        } else {
            //end tempfix
            if ($user = User::create($this->filteredRequest())) {
                $user->notify(new Welcome());
            }
        }

        return $user;
    }

    public function update(User $user)
    {
        if ($user->update($this->filteredRequest())) {
            return $user->fresh();
        }
    }

    public function show(User $user)
    {
        return $user;
    }

    public function destroy(User $user)
    {
        if ($user->delete()) {
            return ['message' => 'Successful deleted'];
        } else {
            return ['message' => 'Deleting failed'];
        }
    }

    public function forceDestroy($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if ((new \App\Http\Controllers\UsersController())->forceDestroy($user, true)) {
            return ['message' => 'Successful deleted'];
        } else {
            return ['message' => 'Deleting failed'];
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
            'Event from curriculum', //event title
            false, //full day event?
            '2019-08-02 10:00:00 UTC+2', //start time, must be a DateTime object or valid DateTime format (http://bit.ly/1z7QWbg)
            '2019-08-02 12:00:00 UTC+2', //end time, must be a DateTime object or valid DateTime format (http://bit.ly/1z7QWbg),
            1, //optional event ID
        ];

        return ['enrollments' => $user->currentGroups()->with(['curricula'])->get(), //todo: select only used fields of curricula
            'notifications' => $user->notifications,
            'events' => [/*$event*/],
        ];
    }

    protected function filteredRequest()
    {
        return array_filter(request()->all()); //filter to ignore fields with null values
    }
}
