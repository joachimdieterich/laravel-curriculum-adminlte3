<?php

namespace App\Http\Controllers;

use App\ObjectiveType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ObjectiveTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->wantsJson()) {
            return ObjectiveType::all()->toJson();
        }
        abort_unless(\Gate::allows('objectivetype_access'), 403);

        return view('objectivetypes.index');
    }

    public function list()
    {
        abort_unless(\Gate::allows('objectivetype_access'), 403);
        $objectivetype = ObjectiveType::select([
            'id',
            'title',
        ])->get();

        $edit_gate = \Gate::allows('objectivetype_edit');
        $delete_gate = \Gate::allows('objectivetype_delete');

        return DataTables::of($objectivetype)
            ->addColumn('action', function ($objectivetype) use ($edit_gate, $delete_gate) {
                $actions = '';
                if ($edit_gate) {
                    $actions .= '<a href="'.route('objectiveTypes.edit', $objectivetype->id).'" '
                        .'id="edit-objectivetype-'.$objectivetype->id.'" '
                        .'class="btn">'
                        .'<i class="fa fa-pencil-alt"></i>'
                        .'</a>';
                }
                if ($delete_gate) {
                    $actions .= '<button type="button" '
                        .'class="btn text-danger" '
                        .'onclick="destroyDataTableEntry(\'objectiveTypes\','.$objectivetype->id.')">'
                        .'<i class="fa fa-trash"></i></button>';
                }

                return $actions;
            })

            ->addColumn('check', '')
            ->setRowId('id')
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_unless(\Gate::allows('objectivetype_create'), 403);

        return view('objectivetypes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('objectivetype_create'), 403);
        $new_type = $this->validateRequest();

        ObjectiveType::create([
            'title' => $new_type['title'],
        ]);

        return redirect()->route('objectiveTypes.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ObjectiveType  $objectiveType
     * @return \Illuminate\Http\Response
     */
    public function show(ObjectiveType $objectiveType)
    {
        abort_unless(\Gate::allows('objectivetype_show'), 403);

        return view('objectiveTypes.show', compact('objectiveType'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ObjectiveType  $objectiveType
     * @return \Illuminate\Http\Response
     */
    public function edit(ObjectiveType $objectiveType)
    {
        abort_unless(\Gate::allows('objectivetype_edit'), 403);

        return view('objectivetypes.edit')
            ->with(compact('objectiveType'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ObjectiveType  $objectiveType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ObjectiveType $objectiveType)
    {
        abort_unless(\Gate::allows('objectivetype_edit'), 403);

        $new_type = $this->validateRequest();
        $objectiveType->update([
            'title' => $new_type['title'],
        ]);

        return redirect()->route('objectiveTypes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ObjectiveType  $objectiveType
     * @return \Illuminate\Http\Response
     */
    public function destroy(ObjectiveType $objectiveType)
    {
        abort_unless(\Gate::allows('objectivetype_delete'), 403);

        $objectiveType->delete();

        return back();
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title' => 'sometimes|required',
        ]);
    }
}
