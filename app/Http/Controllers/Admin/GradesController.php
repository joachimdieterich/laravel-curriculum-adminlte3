<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Grade;

class GradesController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('grade_access'), 403);

        $grades = Grade::all();

        return view('admin.grades.index', compact('grades'));
    }

    public function create()
    {
        abort_unless(\Gate::allows('grade_create'), 403);

        return view('admin.grades.create');
    }

    public function store(StoreGradeRequest $request)
    {
        abort_unless(\Gate::allows('grade_create'), 403);

        $grades = Grade::create($request->all());

        return redirect()->route('admin.grades.index');
    }

    public function edit(Grade $grade)
    {
        abort_unless(\Gate::allows('grade_edit'), 403);

        return view('admin.grades.edit', compact('grade'));
    }

    public function update(UpdateGradeRequest $request, Grade $grade)
    {
        abort_unless(\Gate::allows('grade_edit'), 403);

        $grade->update($request->all());

        return redirect()->route('admin.products.index');
    }

    public function show(Grade $grade)
    {
        abort_unless(\Gate::allows('grade_show'), 403);

        return view('admin.grades.show', compact('grade'));
    }

    public function destroy(Grade $grade)
    {
        abort_unless(\Gate::allows('grade_delete'), 403);

        $grade->delete();

        return back();
    }

    public function massDestroy(MassDestroyGradeRequest $request)
    {
        Grade::whereIn('id', request('ids'))->delete();

        return response(null, 204);
    }
}
