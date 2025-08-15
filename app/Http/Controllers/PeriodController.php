<?php

namespace App\Http\Controllers;

use App\Period;
use Yajra\DataTables\DataTables;

class PeriodController extends Controller
{
    public function index()
    {
        if (request()->wantsJson()) {
            return  getEntriesForSelect2ByModel(
                "App\Period"
            );
        }


        abort_unless(\Gate::allows('period_access'), 403);

        return view('periods.index');
    }

    public function list()
    {
        abort_unless(\Gate::allows('period_access'), 403);
        $periods = Period::select([
            'id',
            'title',
            'begin',
            'end',
        ]);

        return DataTables::of($periods)
            ->addColumn('check', '')
            ->setRowId('id')
            ->setRowAttr([
                'color' => 'primary',
            ])
            ->make(true);
    }


    public function store()
    {
        abort_unless(\Gate::allows('period_create'), 403);
        $new_period = $this->validateRequest();

        $period = Period::firstOrCreate([
            'title' => $new_period['title'],
            'begin' => $new_period['begin'],
            'end' => $new_period['end'],
            //   'organization_id' => format_select_input($new_period['organization_id']),
            'owner_id' => auth()->user()->id,
        ]);

        if (request()->wantsJson()) {
            return $period;
        }
    }



    public function update(Period $period)
    {
        abort_unless(\Gate::allows('period_edit'), 403);
        $new_period = $this->validateRequest();
        $period->update([
            'title' => $new_period['title'],
            'begin' => $new_period['begin'],
            'end' => $new_period['end'],
            // 'organization_id' => format_select_input($new_period['organization_id']),
            'owner_id' => auth()->user()->id,
        ]);

        return $period;
    }

    /**
     * Remove the specified period from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Period $period)
    {
        abort_unless(\Gate::allows('period_delete'), 403);

        return $period->delete();
    }

    /**
     * Display the specified period.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Period $period)
    {
        abort_unless(\Gate::allows('period_access'), 403);

        return view('periods.show')
                ->with(compact('period'));
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title'             => 'sometimes|required',
            'begin'             => 'sometimes',
            'end'               => 'sometimes',
            'owner_id'          => 'sometimes',
        ]);
    }
}
