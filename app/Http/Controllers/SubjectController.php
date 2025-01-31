<?php

namespace App\Http\Controllers;

use App\Subject;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        // select2 request
        if (request()->wantsJson()) {
            return getEntriesForSelect2ByModel(
                "App\Subject"
            );
        }
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

        return DataTables::of($subject)
            ->setRowId('id')
            ->make(true);
    }

    public function getSubject(Request $request)
    {
        abort_unless(\Gate::allows('subject_access'), 403);
        return Subject::select('title')->where('id', $request->id)->get();
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

        $subject = Subject::create([
            'title' => $new_subject['title'],
            'title_short' => $new_subject['title_short'],
            'external_id' => $new_subject['external_id'] ?? 1,
            'organization_type_id' => format_select_input($new_subject['organization_type_id']) ?? 1,
        ]);

        if (request()->wantsJson()) {
            return $subject;
        }
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
            'external_id' => $new_subject['external_id'] ?? 1,
            'organization_type_id' => format_select_input($new_subject['organization_type_id']) ?? 1,
        ]);

        if (request()->wantsJson()) {
            return $subject;
        }
    }

    public function show(Subject $subject)
    {
        abort_unless(\Gate::allows('subject_show'), 403);

        return view('subjects.show', compact('subject'));
    }

    public function destroy(Subject $subject)
    {
        abort_unless(\Gate::allows('subject_delete'), 403);

        return $subject->delete();
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title' => 'sometimes|required',
            'title_short' => 'sometimes',
            'external_id' => 'sometimes',
            'organization_type_id' => 'sometimes',
        ]);
    }
}
