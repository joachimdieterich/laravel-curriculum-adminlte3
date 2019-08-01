<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\User;

class UsersApiController extends Controller
{
    public function index()
    {
        $users = User::all();

        return $users;
    }

    public function store()
    {
        return User::create($this->filteredRequest());
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
    
    protected function filteredRequest() {
        return array_filter(request()->all()); //filter to ignore fields with null values
    }
}
