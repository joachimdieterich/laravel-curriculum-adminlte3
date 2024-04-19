<?php

namespace App\Http\Controllers\Api\V1\Admin;


use App\Http\Controllers\KanbanController;
use App\Http\Controllers\LogbookController;
use App\KanbanSubscription;
use App\LogbookSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Logbook;
use App\Kanban;
use App\User;
use App\Curriculum;
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
        $this->validateRequest();
        $user = User::where('common_name', request()->input('common_name'));

        //return $user->first()->curricula(['curricula.id', 'curricula.title']);

        return Curriculum::where('type_id', 1)->select(['curricula.id', 'curricula.title'])->get();

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

        $return = [];
        foreach($input['groups'] AS $group_id)
        {

            //enrol to curricula
            foreach($input['curricula'] AS $curriculum_id)
            {
                $return['groups'] = Group::findOrFail($group_id)->curricula()->syncWithoutDetaching($curriculum_id);
            }

            //enrol to logbooks
            foreach($input['logbooks'] AS $logbook_id)
            {
                $subscribe = LogbookSubscription::updateOrCreate([
                    'logbook_id' => $logbook_id,
                    'subscribable_type' => "App\Group",
                    'subscribable_id' => $group_id,
                ], [
                    'editable' => $input['editable'] ?? false,
                    'owner_id' => User::where('common_name', $input['common_name'])->get()->first()->id
                ]);
                $return['logbooks'] = $subscribe->save();
            }

            //enrol to kanbans
            foreach($input['kanbans'] AS $kanban_id)
            {
                //dump($kanban_id);
                $subscribe = KanbanSubscription::updateOrCreate([
                    'kanban_id' => $kanban_id,
                    'subscribable_type' => "App\Group",
                    'subscribable_id' => $group_id,
                ], [
                    'editable' => $input['editable'] ?? false,
                    'owner_id' => User::where('common_name', $input['common_name'])->get()->first()->id
                ]);
                $return['kanbans'] =$subscribe->save();
            }
        }
        return $return;
    }

    protected function validateRequest()
    {
        return request()->validate([
            'common_name' => 'required|string',
            'users' => 'sometimes',
            'groups' => 'sometimes',
            'curricula' => 'sometimes',
            'logbooks' => 'sometimes',
            'kanbans' => 'sometimes',
            'editable' => 'sometimes'
        ]);
    }
}
