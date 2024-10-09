<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helpers\QRCodeHelper;
use App\Http\Controllers\Api\V1\OpenApiDefinitions\Organization;
use App\Kanban;
use App\Http\Controllers\Controller;
use App\KanbanSubscription;
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

    public function enrolToKanban(Kanban $kanban){
        $model = $this->resolveModel(request()->input('subscribable_type'));

        $subscribe = KanbanSubscription::updateOrCreate([
            'kanban_id' => $kanban->id,
            'subscribable_type' => request()->input('subscribable_type'),
            'subscribable_id' => $model->id,
        ], [
            'editable' => request()->input('editable') ? 1 : 0,
            'owner_id' => auth()->user()->id ?? 1,
        ]);
        return $subscribe->save();
    }

    public function expel()
    {
        $user = User::find(request()->input('user_id'));
        if ($user->kanbans()->detach(['kanban_id' => request()->input('kanban_id')])) {
            return ['message' => 'Successful expelled'];
        }
    }

    public function expelFromKanban(Kanban $kanban){
        $model = $this->resolveModel(request()->input('subscribable_type'));

        $subscribe = KanbanSubscription::where([
            'kanban_id' => $kanban->id,
            'subscribable_type' => request()->input('subscribable_type'),
            'subscribable_id' => $model->id,
        ]);
        return $subscribe->delete();
    }

    public function subscriptions(Kanban $kanban)
    {
        $tokens = null;
        $tokenscodes = KanbanSubscription::where('kanban_id', request('kanban_id'))
            ->where('sharing_token', "!=", null)
            ->get();

        foreach ($tokenscodes as $token)
        {
            $tokens[] = [
                "token" => $token,
                "qr"    => (new QRCodeHelper())
                    ->generateQRCodeByString(
                        env("APP_URL"). "/kanbans/" . request('kanban_id') ."/token?sharing_token=" .$token->sharing_token
                    )
            ];
        }

        return [
            'subscribers' => [
                'tokens' => $tokens ?? [],
                'subscriptions' => optional(
                    optional(
                        Kanban::find($kanban->id)
                    )->subscriptions()
                )->with('subscribable')
                    ->whereHasMorph('subscribable', '*', function ($q, $type) {
                        if ($type == 'App\\User') {
                            $q->whereNot('id', env('GUEST_USER'));
                        }
                    })->get(),
            ],
        ];
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

    private function resolveModel($model)
    {
        switch ($model) {
            case 'App\Organization':
                return Organization::where('common_name', request()->input('common_name'))->firstOrFail();
                break;
            case 'App\Group':
                return Group::where('common_name', request()->input('common_name'))->firstOrFail();
                break;
            case 'App\User':
                return User::where('common_name', request()->input('common_name'))->firstOrFail();
                break;
            default: return false;
        }
    }
}
