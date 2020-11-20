<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Organization;
use App\OrganizationRoleUser;
use App\User;

class OrganizationsApiController extends Controller {

    public function index()
    {
        $organizations = Organization::all();

        return $organizations;
    }

    public function store()
    {
        return Organization::create($this->filteredRequest());
    }

    public function update(Organization $organization)
    {
        if ($organization->update($this->filteredRequest()))
        {
            return $organization->fresh();
        }
    }

    public function show(Organization $organization)
    {
        return $organization;
    }

    public function destroy(Organization $organization)
    {
        if ($organization->delete()) {
            return ['message' => 'Successful deleted'];
        }
    }

    public function enrol()
    {
        return OrganizationRoleUser::updateOrCreate(
                [
                        'user_id'         => request('user_id'),
                        'organization_id' => request('organization_id')
                    ],
                    [
                        'role_id'         => request('role_id')
                    ]
                );
    }

    public function expel()
    {
        if (OrganizationRoleUser::where(request()->all())->delete())
        {
            //reset current org id to prevent error based "on current_organization_id"
            $user = User::where('id', request('user_id'))->get()->first();
            $user->current_organization_id = NULL;
            $user->current_period_id = NULL;
            $user->save();

            return ['message' => 'Successful expelled'];

        }
    }

    public function members(Organization $organization) {
        return $organization->users;
    }

    protected function filteredRequest() {
        return array_filter(request()->all()); //filter to ignore fields with null values
    }

}
