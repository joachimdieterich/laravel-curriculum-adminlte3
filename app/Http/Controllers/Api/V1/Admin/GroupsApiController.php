<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Group;
use App\Http\Controllers\Controller;
use App\OrganizationRoleUser;
use App\Period;
use App\User;
use Carbon\Carbon;

class GroupsApiController extends Controller
{
    public function index()
    {
        return Group::all();
    }

    public function store()
    {
        return Group::firstOrCreate([
            'title'           => request()->input('title'),
            'grade_id'        => request()->input('grade_id'),
            'period_id'       => $this->getPeriod()->id,
            'organization_id' => request()->input('organization_id'),
            'common_name'     => request()->input('common_name'),
        ]);
    }

    public function update(Group $group)
    {
        if (
            $group->update([
                'title'           => (request()->input('title')) ?: $group->title,
                'grade_id'        => (request()->input('grade_id')) ?: $group->grade_id,
                'period_id'       => ($this->getPeriod()->id) ?: $group->period_id,
                'organization_id' => (request()->input('organization_id')) ?: $group->organization_id,
                'common_name'     => (request()->input('common_name')) ?: $group->common_name,
            ])
        ) {
            return $group->fresh();
        }
    }

    public function show(Group $group)
    {
        return $group;
    }

    public function members(Group $group)
    {
        return $group->users;
    }

    public function destroy(Group $group): ?array
    {
        return ['message' => $group->delete() ? 'Successful deleted' : 'Deletion failed'];
    }

    public function enrol()
    {
        $group = Group::findOrFail(request()->input('group_id'));
        $user  = User::findOrFail(request()->input('user_id'));

        OrganizationRoleUser::firstOrCreate(
            [
                'user_id'         => $user->id,
                'organization_id' => $group->organization->id,
            ],
            ['role_id' => 6], // enrol as student
        );

        $return[] = $user->groups()->syncWithoutDetaching(request()->input('group_id'));

        return $return;
    }

    public function expel()
    {
        $user = User::find(request()->input('user_id'));
        if ($user->groups()->detach(['group_id' => request()->input('group_id')])) {
            return ['message' => 'Successful expelled'];
        }
    }

    /**
     * @return Period
     */
    private function getPeriod()
    {
        if ((request()->input('period')) && strtolower(request()->input('period')) != 'null') {
            $dates = explode('/', request()->input('period'), 2); // get begin and end of period

            return Period::firstOrCreate(
                [
                    'title' => request()->input('period'),
                ],
                [
                    'begin'    => Carbon::createFromDate(ltrim($dates[0], '('))->format('Y-m-d h:m:s'),
                    'end'      => Carbon::createFromDate(rtrim($dates[1], ')'))->format('Y-m-d h:m:s'),
                    'owner_id' => 1, // api call
                ]);
        }

        if ((request()->input('period_id')) && strtolower(request()->input('period_id')) != 'null') {
            return Period::find(request()->input('period_id'));
        }

        return Period::find(1); // fallback
    }
}
