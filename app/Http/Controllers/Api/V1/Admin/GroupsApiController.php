<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Grade;
use App\Http\Controllers\Controller;
use App\Group;
use App\Period;
use App\User;
use App\OrganizationRoleUser;
use Carbon\Carbon;


class GroupsApiController extends Controller {

    public function index() {
        $groups = Group::all();

        return $groups;
    }

    public function store() {

        return Group::firstOrCreate([
            'title'             => request()->input('title'),
            'grade_id'          => request()->input('grade_id'),
            'period_id'         => $this->getPeriod()->id,
            'organization_id'   => request()->input('organization_id')
        ]);

        //return Group::create($this->filteredRequest());
    }

    public function update(Group $group) {

        if (
            $group->update([
                'title'             => (request()->input('title')) ?: $group->title,
                'grade_id'          => (request()->input('grade_id')) ?: $group->grade_id,
                'period_id'         => ($this->getPeriod()->id) ?: $group->period_id,
                'organization_id'   => (request()->input('organization_id')) ?: $group->organization_id
            ])
        )
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

        // first delete all relations
        $group->curricula()->detach();
        $group->users()->detach();

        //todo: delete subscriptions ( eg. kanban), yet no relation in Group.php

        if ($group->delete())
        {
            return ['message' => 'Successful deleted'];
        }
    }

    public function enrol()
    {
        $group = Group::findOrFail(request()->input('group_id'));
        $user = User::findOrFail(request()->input('user_id'));

        OrganizationRoleUser::firstOrCreate([
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

    /**
     * @return Period
     */
    private function getPeriod()
    {

        if ((request()->input('period')) AND strtolower(request()->input('period')) != 'null')
        {
            $dates = explode("/", request()->input('period'), 2); //get begin and end of period

            return Period::firstOrCreate(
                [
                    'title' => request()->input('period'),
                ],
                [
                    'begin' => Carbon::createFromDate($dates[0])->format('Y-m-d h:m:s'),
                    'end' => Carbon::createFromDate($dates[1])->format('Y-m-d h:m:s'),
                    'owner_id' => 1 //api call
                ]);
        }
        else if ((request()->input('period_id')) AND  strtolower(request()->input('period_id')) != 'null')
        {
            return Period::find(request()->input('period_id'));
        }
        else
        {
            return Period::find(1); //fallback
        }

    }

}
