<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\KanbanController;
use App\Http\Controllers\LogbookController;
use App\Kanban;
use App\KanbanSubscription;
use App\Curriculum;
use App\CurriculumSubscription;
use App\LogbookSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\User;
use App\Group;

class MoodleApiController extends Controller
{
    public function getModelTypes()
    {
        $modelTypes = [
            [
                "model" => "App\Curriculum",
                "url"   => "/curricula",
                "title"	=> trans('global.curriculum.title'),
            ],
            [
                "model" => "App\TerminalObjective",
                "url"	=> "/terminalObjectives",
                "title"	=> trans('global.terminalObjective.title'),
            ],
            [
                "model" => "App\EnabingObjective",
                "url"	=> "/enablingObjectives",
                "title"	=> trans('global.enablingObjective.title'),
            ],
            [
                "model" => "App\Logbook",
                "url"	=> "/logbooks",
                "title"	=> trans('global.logbook.title'),
            ],
            [
                "model" => "App\Kanban",
                "url"	=> "/kanbans",
                "title"	=> trans('global.kanban.title'),
            ]
        ];

        return $modelTypes;
    }

    public function getCurricula(Request $request)
    {
        $input = $this->validateRequest();
        $user_id = User::select('id')->where('common_name', $input['common_name'])->first()->id;

        //return $user->first()->curricula(['curricula.id', 'curricula.title']);

        return Curriculum::select('id', 'title')->without('owner')->where('type_id', 1)->orWhere('owner_id', $user_id)->get()->makeHidden(['is_favourited']);
    }

    public function getTerminalObjectives(\App\Curriculum $curriculum,Request $request)
    {
        $this->validateRequest();
        $user = User::where('common_name', request()->input('common_name'));
        Auth::loginUsingId($user->first()->id);
        $curriculum->isAccessible();

        return $curriculum->terminalObjectives()
            ->with('enablingObjectives')
            ->get();
        //return $curriculum->terminalObjectives->map->only('id', 'title');
    }

    public function getEnablingObjectivesByTerminalObjectiveId(\App\TerminalObjective $terminalObjective, Request $request)
    {
        $this->validateRequest();
        $user = User::where('common_name', request()->input('common_name'));
        Auth::loginUsingId($user->first()->id);
        $terminalObjective->curriculum->isAccessible();

        return $terminalObjective->enablingObjectives->map->only('id', 'title');
    }

    public function getEnablingObjectives(\App\Curriculum $curriculum, Request $request)
    {
        $this->validateRequest();
        $user = User::where('common_name', request()->input('common_name'));
        Auth::loginUsingId($user->first()->id);
        $curriculum->isAccessible();

        return $curriculum->enablingObjectives->map->only('id', 'title', 'terminal_objective_id');
    }

    public function getLogbooks(Request $request)
    {
        $this->validateRequest();
        $user = User::where('common_name', request('common_name'));
        $user = Auth::loginUsingId($user->first()->id);


        /*$logbooks = Logbook::where('owner_id', $user->id )
                        ->select('id', 'title')->get()
                        ->merge(
                            $user->logbooks()
                            ->select('logbooks.id', 'title')->get()
                        );*/
        $logbooks = (new LogbookController())->getLogbooks()->get(); //get all accessible logbooks

        return $logbooks->map->only('id', 'title')->unique('id');


    }

    public function getKanbans(Request $request)
    {
        $this->validateRequest();
        $user = User::where('common_name', request('common_name'));
        $user = Auth::loginUsingId($user->first()->id);


        $kanbans = (new KanbanController())->userKanbans(); //get all accessible kanbans
        /*$kanbans = Kanban::where('owner_id', $user->id )
            ->select('id', 'title')->get()
            ->merge(
                $user->kanbans()
                    ->select('kanbans.id', 'kanbans.title')->get()
            );*/
        return $kanbans->map->only('id', 'title')->unique('id');

    }

    public function getGroups(Request $request)
    {
        $this->validateRequest();
        $user = User::where('common_name', request('common_name'));
        $user = Auth::loginUsingId($user->first()->id);

        $groups = $user->groups()
            ->select('groups.id', 'groups.title')->get();

        return $groups->map->only('id', 'title')->unique('id');
    }

    public function enrolToGroup(Request $request)
    {
        $input = $this->validateRequest();
        $groups = Group::whereIn('common_name', $input['groups'])->pluck('id');
        $owner = User::where('common_name', $input['common_name'])->first()->id;
        $response = [];

        foreach($groups AS $group_id)
        {
            // enrol to curricula
            // foreach($input['curricula'] AS $curriculum_id)
            // {
            //     $response['groups'] = Group::findOrFail($group_id)->curricula()->syncWithoutDetaching($curriculum_id);
            // }

            // enrol to logbooks
            if (!empty($input['logbooks'])) {
                $logbooks = [];

                foreach($input['logbooks'] AS $logbook_id)
                {
                    $subscribe = LogbookSubscription::updateOrCreate([
                        'logbook_id' => $logbook_id,
                        'subscribable_type' => "App\Group",
                        'subscribable_id' => $group_id,
                    ], [
                        'editable' => $input['editable'] ?? false,
                        'owner_id' => $owner,
                    ]);
                    array_push($logbooks, $subscribe->save());
                }

                $response['logbooks'] = $logbooks;
            }

            // enrol to kanbans
            if (!empty($input['kanbans'])) {
                $kanbans = [];

                foreach($input['kanbans'] AS $kanban_id)
                {
                    $subscribe = KanbanSubscription::updateOrCreate([
                        'kanban_id' => $kanban_id,
                        'subscribable_type' => "App\Group",
                        'subscribable_id' => $group_id,
                    ], [
                        'editable' => $input['editable'] ?? false,
                        'owner_id' => $owner,
                    ]);
                    array_push($kanbans, $subscribe->save());
                }

                $response['kanbans'] = $kanbans;
            }
        }

        return $response;
    }

    public function enrolUsers(Request $request) {
        $input = $this->validateRequest();
        // validate that required fields are present
        if (empty($input['users'])) abort(400, 'No users (common_name) provided');
        if (empty($input['kanbans']) and empty($input['curricula'])) abort(400, 'At least one model needs to be provided (kanbans or curricula)');

        // parse fields to arrays if they are strings
        if (gettype($input['users']) == 'string') $input['users'] = json_decode($input['users'], true);
        if (!empty($input['kanbans']) and gettype($input['kanbans']) == 'string') $input['kanbans'] = json_decode($input['kanbans'], true);
        if (!empty($input['curricula']) and gettype($input['curricula']) == 'string') $input['curricula'] = json_decode($input['curricula'], true);

        $users = User::select('id', 'common_name')->whereIn('common_name', $input['users'])->get();
        // if not every common_name has a corresponding user, return those
        if (count($users) != count($input['users'])) {
            $missing = array_diff($input['users'], $users->pluck('common_name')->toArray());
            abort(400, 'Users with common names ['.implode(', ', $missing).'] not found');
        }

        $create_count = 0;

        if (!empty($input['kanbans'])) {
            // create subscriptions for kanbans
            foreach ($input['kanbans'] as $kanban_id) {
                // check if kanban exists
                $owner_id = Kanban::select('owner_id')->find($kanban_id)?->owner_id;
                if (empty($owner_id)) abort(400, 'Kanban with ID '.$kanban_id.' not found');
    
                foreach ($users as $user) {
                    KanbanSubscription::updateOrCreate([
                        'kanban_id' => $kanban_id,
                        'subscribable_type' => "App\User",
                        'subscribable_id' => $user->id,
                    ], [
                        'editable' => $input['editable'] ?? false,
                        'owner_id' => $owner_id,
                    ]);
    
                    $create_count++;
                }
            }
        }

        if (!empty($input['curricula'])) {
            // create subscriptions for curricula
            foreach ($input['curricula'] as $curriculum_id) {
                // check if curricula exists
                $owner_id = Curriculum::select('owner_id')->find($curriculum_id)?->owner_id;
                if (empty($owner_id)) abort(400, 'Curriculum with ID '.$curriculum_id.' not found');
    
                foreach ($users as $user) {
                    CurriculumSubscription::updateOrCreate([
                        'curriculum_id' => $curriculum_id,
                        'subscribable_type' => "App\User",
                        'subscribable_id' => $user->id,
                    ], [
                        'owner_id' => $owner_id,
                    ]);
    
                    $create_count++;
                }
            }
        }

        return $create_count;
    }

    public function expelUsers(Request $request) {
        $input = $this->validateRequest();
        // validate that required fields are present
        if (empty($input['users'])) abort(400, 'No users (common_name) provided');
        if (empty($input['kanbans']) and empty($input['curricula'])) abort(400, 'At least one model needs to be provided (kanbans or curricula)');

        // parse fields to arrays if they are strings
        if (gettype($input['users']) == 'string') $input['users'] = json_decode($input['users'], true);
        if (!empty($input['kanbans']) and gettype($input['kanbans']) == 'string') $input['kanbans'] = json_decode($input['kanbans'], true);
        if (!empty($input['curricula']) and gettype($input['curricula']) == 'string') $input['curricula'] = json_decode($input['curricula'], true);

        $users = User::select('id', 'common_name')->whereIn('common_name', $input['users'])->get();
        // if not every common_name has a corresponding user, return those
        if (count($users) != count($input['users'])) {
            $missing = array_diff($input['users'], $users->pluck('common_name')->toArray());
            abort(400, 'Users with common names ['.implode(', ', $missing).'] not found');
        }

        $delete_count = 0;

        if (!empty($input['kanbans'])) {
            foreach ($input['kanbans'] as $kanban_id) {
                // check if kanban exists
                $owner_id = Kanban::select('owner_id')->find($kanban_id)?->owner_id;
                if (empty($owner_id)) abort(400, 'Kanban with ID '.$kanban_id.' not found');
    
                $delete_count += KanbanSubscription::where([
                    'kanban_id' => $kanban_id,
                    'subscribable_type' => "App\User",
                ])
                ->whereIn('subscribable_id', $users->pluck('id')->toArray())
                ->delete();
            }
        }

        if (!empty($input['curricula'])) {
            foreach ($input['curricula'] as $curriculum_id) {
                // check if curriculum exists
                $owner_id = Curriculum::select('owner_id')->find($curriculum_id)?->owner_id;
                if (empty($owner_id)) abort(400, 'Curriculum with ID '.$curriculum_id.' not found');
    
                $delete_count += CurriculumSubscription::where([
                    'curriculum_id' => $curriculum_id,
                    'subscribable_type' => "App\User",
                ])
                ->whereIn('subscribable_id', $users->pluck('id')->toArray())
                ->delete();
            }
        }

        return $delete_count;
    }

    protected function validateRequest()
    {
        return request()->validate([
            'common_name' => 'sometimes|string',
            'users' => is_array(request()->input('users')) ? 'sometimes|array' : 'sometimes|string',
            'groups' => 'sometimes|array',
            'curricula' => is_array(request()->input('curricula')) ? 'sometimes|array' : 'sometimes|string',
            'logbooks' => 'sometimes|array',
            'kanbans' => is_array(request()->input('kanbans')) ? 'sometimes|array' : 'sometimes|string',
            'editable' => 'sometimes|boolean',
        ]);
    }
}