<?php

namespace App\Http\Controllers\Api\V1\Admin;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Logbook;
use App\Kanban;
use App\User;


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

        return $user->first()->curricula(['curricula.id', 'curricula.title']);
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

        return $curriculum->enablingObjectives->map->only('id', 'title');
    }

    public function getLogbooks(Request $request)
    {
        $this->validateRequest();
        $user = User::where('common_name', request('common_name'));
        $user = Auth::loginUsingId($user->first()->id);

        $logbooks = Logbook::where('owner_id', $user->id )
                        ->select('id', 'title')->get()
                        ->merge(
                            $user->logbooks()
                            ->select('logbooks.id', 'title')->get()
                        );
        return $logbooks->map->only('id', 'title')->unique('id');


    }

    public function getKanbans(Request $request)
    {
        $this->validateRequest();
        $user = User::where('common_name', request('common_name'));
        $user = Auth::loginUsingId($user->first()->id);


        $kanbans = Kanban::where('owner_id', $user->id )
            ->select('id', 'title')->get()
            ->merge(
                $user->kanbans()
                    ->select('kanbans.id', 'title')->get()
            );
        return $kanbans->map->only('id', 'title')->unique('id');

    }

    protected function validateRequest()
    {
        return request()->validate([
            'common_name' => 'required|string',
        ]);
    }
}
