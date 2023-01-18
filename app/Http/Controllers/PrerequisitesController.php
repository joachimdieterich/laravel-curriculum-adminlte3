<?php

namespace App\Http\Controllers;

use App\Prerequisites;
use Illuminate\Http\Request;

class PrerequisitesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $request = $this->validateRequest();
        $data = $this->generateD3Data($request);
        if (request()->wantsJson()) {
            return [
                'prerequisites' => $data,
            ];
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('prerequisite_create'), 403);
        $new_prerequisite = $this->validateRequest();

        $subscribe = Prerequisites::firstOrCreate([
            'predecessor_type'  => $this->getModelType($new_prerequisite),
            'predecessor_id'    => $this->getModelId($new_prerequisite),
            'successor_type'    => $new_prerequisite['successor_type'],
            'successor_id'      => $new_prerequisite['successor_id'],
        ], [
            'owner_id'          => auth()->user()->id,
        ]);
        $subscribe->save();
    }

    protected function getModelType($validated_request)
    {
        if ($validated_request['enabling_objective_id'] != null) {
            return "App\EnablingObjective";
        } else {
            return ($validated_request['terminal_objective_id'] != null) ? "App\TerminalObjective" : "App\Curriculum";
        }
    }

    protected function getModelId($validated_request)
    {
        if ($validated_request['enabling_objective_id'] != null) {
            return $validated_request['enabling_objective_id'];
        } else {
            return ($validated_request['terminal_objective_id'] != null) ? $validated_request['terminal_objective_id'] : $validated_request['curriculum_id'];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Prerequisites  $prerequisites
     * @return \Illuminate\Http\Response
     */
    public function destroy(Prerequisites $prerequisites)
    {
        abort_unless(\Gate::allows('prerequisite_delete'), 403);
        $entry = $prerequisites->get()->first();
        $entry->delete();
    }

    protected function generateD3Data($request)
    {
        $successor = app()->make($request['successor_type'])::find($request['successor_id']);

        /*  $prerequisites = Prerequisites::where([
              'successor_type' => $request['successor_type'],
              'successor_id'   => $request['successor_id'],
          ])->with(['predecessor'])->get();*/

        $data = [
            'id' => $successor->id,
            'name' => $this->resolveType($request['successor_type'], $successor),
            'type' => 'Root',
            'description' => strip_tags($successor->title),
            'children' => $this->predecessor($successor->predecessors),
        ];

        return $data;
    }

    protected function predecessor($predecessors, $level = 0, $max_iterations = 5)
    {
        $data = [];

        foreach ($predecessors as $predecessor) {
            $data[] = [
                'id' => $predecessor->predecessor_id,
                'name' => $this->resolveType($predecessor->predecessor_type, $predecessor->predecessor),
                'type' => 'Level '.$level,
                'prerequisite_id' => $predecessor->id,
                'description' => strip_tags($predecessor->predecessor->title),
                'children' => $this->predecessor($predecessor->predecessor->predecessors, $level + 1),
            ];
        }

        return $data;
    }

    protected function resolveType($type, $model)
    {
        switch ($type) {
            case "App\Curriculum":
                //$typeName = trans('global.curriculum.title_singular');
                $typeName = $model->title;
                break;
            case "App\TerminalObjective":
                //$typeName = trans('global.terminalObjective.title_singular');
                $typeName = $model->curriculum->title;
                break;
            case "App\EnablingObjective":
                //$typeName = trans('global.enablingObjective.title_singular');
                $typeName = $model->curriculum->title;
                break;
        }

        return $typeName;
    }

    protected function validateRequest()
    {
        return request()->validate([
            'id'                    => 'sometimes',
            'curriculum_id'         => 'sometimes',
            'terminal_objective_id' => 'sometimes',
            'enabling_objective_id' => 'sometimes',
            'successor_type'        => 'sometimes',
            'successor_id'          => 'sometimes',
            'owner_id'              => 'sometimes',
        ]);
    }
}
