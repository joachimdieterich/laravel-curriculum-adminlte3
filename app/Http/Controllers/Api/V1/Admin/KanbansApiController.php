<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\KanbanController;
use App\Kanban;
use App\Http\Controllers\Controller;
use App\OrganizationRoleUser;
use App\Period;
use App\User;
use Carbon\Carbon;

class KanbansApiController extends Controller
{
    public function index()
    {
        $user = User::where('common_name', request()->input('owner_cn'))->get()->first();

        return  Kanban::where('owner_id', $user->id)->get();
    }

    public function store()
    {
        $user = User::where('common_name', request()->input('owner_cn'))->get()->first();

        return Kanban::firstOrCreate([
            'title'             => request()->input('title'),
            'description'       => request()->input('description'),
            'color'             => request()->input('color') ?? '#2980B9',
            'owner_id'          => $user->id,
        ]);
    }

    public function update(Kanban $kanban)
    {
        if (
            $kanban->update([
                'title'         => (request()->input('title')) ?: $kanban->title,
                'description'   => (request()->input('description')) ?: $kanban->description,
                'color'         => (request()->input('color')) ?: $kanban->color,
            ])
        ) {
            return $kanban->fresh();
        }
    }

    public function show(Kanban $kanban)
    {
        return $kanban;
    }

    public function destroy(Kanban $kanban)
    {

        //delete relations
        $kanban->items()->delete();
        $kanban->statuses()->delete();
        $kanban->subscriptions()->delete();

        if ($kanban->delete()) {
            return ['message' => 'Successful deleted'];
        }
    }

    public function enrol()
    {
        $kanban = Kanban::findOrFail(request()->input('kanban_id'));
        $user = User::findOrFail(request()->input('user_id'));

        OrganizationRoleUser::firstOrCreate([
            'user_id' => $user->id,
            'organization_id' => $kanban->organization->id, ],
            ['role_id' => 6], //enrol as student
        );

        $return[] = $user->kanbans()->syncWithoutDetaching(request()->input('kanban_id'),
        );

        return $return;
    }

    public function expel()
    {
        $user = User::find(request()->input('user_id'));
        if ($user->kanbans()->detach(['kanban_id' => request()->input('kanban_id')])) {
            return ['message' => 'Successful expelled'];
        }
    }

    protected function filteredRequest()
    {
        return array_filter(request()->all()); //filter to ignore fields with null values
    }

    /**
     * @return Period
     */
    private function getPeriod()
    {
        if ((request()->input('period')) and strtolower(request()->input('period')) != 'null') {
            $dates = explode('/', request()->input('period'), 2); //get begin and end of period

            return Period::firstOrCreate(
                [
                    'title' => request()->input('period'),
                ],
                [
                    'begin' => Carbon::createFromDate($dates[0])->format('Y-m-d h:m:s'),
                    'end' => Carbon::createFromDate($dates[1])->format('Y-m-d h:m:s'),
                    'owner_id' => 1, //api call
                ]);
        } elseif ((request()->input('period_id')) and strtolower(request()->input('period_id')) != 'null') {
            return Period::find(request()->input('period_id'));
        } else {
            return Period::find(1); //fallback
        }
    }
}
