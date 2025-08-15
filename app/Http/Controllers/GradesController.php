<?php

namespace App\Http\Controllers;

use App\CurriculumType;
use App\Grade;
use Yajra\DataTables\DataTables;

class GradesController extends Controller
{
    public function index()
    {
        if (request()->wantsJson()) {
            return getEntriesForSelect2ByModel(
                "App\Grade"
            );
        }
        abort_unless(\Gate::allows('grade_access'), 403);

        return view('grades.index');
    }

    public function list()
    {
        abort_unless(\Gate::allows('grade_access'), 403);
        $grades = Grade::select([
            'id',
            'title',
            'external_begin',
            'external_end',
            'organization_type_id',
        ])->with('organizationType')->get();

        return DataTables::of($grades)
            ->addColumn('organization_type', function ($grades) {
                return isset($grades->organizationType->title) ? $grades->organizationType->title : 'default';
            })
            ->addColumn('check', '')
            ->setRowId('id')
            ->make(true);
    }

    public function store()
    {
        abort_unless(\Gate::allows('grade_create'), 403);
        $new_grade = $this->validateRequest();

        $grades = Grade::create([
            'title' => $new_grade['title'],
            'external_begin' => $new_grade['external_begin'],
            'external_end' => $new_grade['external_end'],
            'organization_type_id' => format_select_input($new_grade['organization_type_id']),
        ]);

        if (request()->wantsJson()) {
            return $grades;
        }
    }

    public function update(Grade $grade)
    {
        abort_unless(\Gate::allows('grade_edit'), 403);

        $new_grade = $this->validateRequest();
        $grade->update([
            'title' => $new_grade['title'],
            'external_begin' => $new_grade['external_begin'],
            'external_end' => $new_grade['external_end'],
            'organization_type_id' => format_select_input($new_grade['organization_type_id']),
        ]);

        return $grade;
    }

    public function show(Grade $grade)
    {
        abort_unless(\Gate::allows('grade_show'), 403);

        $grade = $grade->load('organizationType');

        return view('grades.show', compact('grade'));
    }

    public function destroy(Grade $grade)
    {
        abort_unless(\Gate::allows('grade_delete'), 403);

        $grade->delete();

        return $grade->delete();
    }

    public function massDestroy()
    {
        Grade::whereIn('id', request('ids'))->delete();

        return response(null, 204);
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title'                  => 'sometimes|required',
            'external_begin'         => 'sometimes|required',
            'external_end'           => 'sometimes|required',
            'organization_type_id'   => 'sometimes|required',
        ]);
    }
}
