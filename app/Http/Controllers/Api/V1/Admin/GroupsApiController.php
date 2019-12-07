<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Group;
use App\User;
use App\OrganizationRoleUser;


class GroupsApiController extends Controller {

    public function index() {
        $groups = Group::all();

        return $groups;
    }

    public function store() {
        return Group::create($this->filteredRequest());
    }

    public function update(Group $group) {
        if ($group->update($this->filteredRequest())) 
        { 
            return $group->fresh();
        }
    }

    public function show(Group $group) {
        return $group;
    }
    
    public function members(Group $group) {
        return $group->users;
    }

    public function destroy(Group $group) {
        if ($group->delete()) 
        {
            return ['message' => 'Successful deleted'];
        }
    }
    
    public function enrol() 
    {
        $group = Group::findOrFail(request()->input('group_id'));
        $user = User::findOrFail(request()->input('user_id'));

        OrganizationRoleUser::updateOrCreate([
            'user_id' => $user->id,
            'organization_id' => $group->organization->id],
            ['role_id' => 6], //enrol as student
        );

        $return[] = $user->groups()->syncWithoutDetaching(request()->input('group_id'),
        );
        return $return;
    }

    public function expel() 
    {
        $user = User::find(request()->input('user_id'));
        if ($user->groups()->detach(['group_id' => request()->input('group_id')]))
        {
            return ['message' => 'Successful expelled'];
        }
    }
    

    protected function filteredRequest() {
        return array_filter(request()->all()); //filter to ignore fields with null values
    }

}
