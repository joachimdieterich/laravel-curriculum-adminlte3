<?php

namespace App\Http\Controllers;

use App\Subject;
use Yajra\DataTables\DataTables;

class SubjectController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('subject_access'), 403);

        return view('subjects.index');
    }

    public function list()
    {
        abort_unless(\Gate::allows('subject_access'), 403);
        $subject = Subject::select([
            'id',
            'title',
            'title_short',
        ])->get();

        $edit_gate = \Gate::allows('subject_edit');
        $delete_gate = \Gate::allows('subject_delete');

        return DataTables::of($subject)
            ->addColumn('action', function ($subject) use ($edit_gate, $delete_gate) {
                $actions = '';
                if ($edit_gate) {
                    $actions .= '<a href="'.route('subjects.edit', $subject->id).'" '
                        .'id="edit-subject-'.$subject->id.'" '
                        .'class="btn">'
                        .'<i class="fa fa-pencil-alt"></i>'
                        .'</a>';
                }
                if ($delete_gate) {
                    $actions .= '<button type="button" '
                        .'class="btn text-danger" '
                        .'onclick="destroyDataTableEntry(\'subjects\','.$subject->id.');">'
                        .'<i class="fa fa-trash"></i></button>';
                }

                return $actions;
            })

            ->addColumn('check', '')
            ->setRowId('id')
            ->make(true);
    }

    public function create()
    {
        abort_unless(\Gate::allows('subject_create'), 403);

        return view('subjects.create');
    }

    public function store()
    {
        abort_unless(\Gate::allows('subject_create'), 403);
        $new_subject = $this->validateRequest();

        Subject::create([
            'title' => $new_subject['title'],
            'title_short' => $new_subject['title_short'],
            'external_id' => isset($new_subject['external_id']) ? $new_subject['external_id'] : 1,
            'organization_type_id' => 1, // todo: is this used?

        ]);

        return redirect()->route('subjects.index');
    }

    public function edit(Subject $subject)
    {
        abort_unless(\Gate::allows('subject_edit'), 403);

        return view('subjects.edit')
            ->with(compact('subject'));
    }

    public function update(Subject $subject)
    {
        abort_unless(\Gate::allows('subject_edit'), 403);

        $new_subject = $this->validateRequest();
        $subject->update([
            'title' => $new_subject['title'],
            'title_short' => $new_subject['title_short'],
            'external_id' => isset($new_subject['external_id']) ? $new_subject['external_id'] : 1,
            'organization_type_id' => 1, // todo: is this used?
        ]);

        return redirect()->route('subjects.index');
    }

    public function show(Subject $subject)
    {
        abort_unless(\Gate::allows('subject_show'), 403);

        return view('subjects.show', compact('subject'));
    }

    public function destroy(Subject $subject)
    {
        abort_unless(\Gate::allows('subject_delete'), 403);

        $subject->delete();

        return back();
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title' => 'sometimes|required',
            'title_short' => 'sometimes|required',
            'external_id' => 'sometimes',
            'organization_type_id' => 'sometimes',

        ]);
    }
}
