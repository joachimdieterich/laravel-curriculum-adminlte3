<?php

namespace App\Http\Controllers;

use App\Organization;
use App\Period;
use Yajra\DataTables\DataTables;


class PeriodController extends Controller
{
    public function index()
    {
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
            'organization_id',
            ]);       
        
        return DataTables::of($periods)
            ->addColumn('organization', function ($periods) {
                return isset($periods->organization()->first()->title) ? $periods->organization()->first()->title : 'global';                
            })
            ->addColumn('action', function ($periods) {
                 $actions  = '';
                    if (\Gate::allows('period_show')){
                        $actions .= '<a href="'.route('periods.show', $periods->id).'" '
                                    . 'id="show-period-'.$periods->id.'" '
                                    . 'class="btn btn-xs btn-success mr-1">'
                                    . '<i class="fa fa-list-alt"></i>'
                                    . '</a>';
                    }
                    if (\Gate::allows('period_edit')){
                        $actions .= '<a href="'.route('periods.edit', $periods->id).'" '
                                    . 'id="edit-period-'.$periods->id.'" '
                                    . 'class="btn btn-xs btn-primary mr-1">'
                                    . '<i class="fa fa-edit"></i>'
                                    . '</a>';
                    }
                    if (\Gate::allows('period_delete')){
                        $actions .= '<form action="'.route('periods.destroy', $periods->id).'" method="POST" class="float-right">'
                                    . '<input type="hidden" name="_method" value="delete">'. csrf_field().''
                                    . '<button '
                                    . 'type="submit" '
                                    . 'id="delete-period-'.$periods->id.'" '
                                    . 'class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></button>';
                    }
              
                return $actions;
            })
           
            ->addColumn('check', '')
            ->setRowId('id')
            ->setRowAttr([
                'color' => 'primary',
            ])
            ->make(true);
    }
    
    /**
     * Show the form for creating a new period.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_unless(\Gate::allows('period_create'), 403);

        $organizations = auth()->user()->organizations()->get();
        return view('periods.create')
                    ->with(compact('organizations'));
    }
    
    public function store()
    {
        abort_unless(\Gate::allows('period_create'), 403);
        $new_period = $this->validateRequest();
        
        $period = Period::firstOrCreate([
            'title' => $new_period['title'],
            'begin' => $new_period['begin'],
            'end' => $new_period['end'],
            'organization_id' => format_select_input($new_period['organization_id']),
            'owner_id' => auth()->user()->id
        ]);
        
        // axios call? 
        if (request()->wantsJson()){    
            return ['message' => $period->path()];
        }
        
        return redirect($period->path());
    }
    
    /**
     * Show the form for updating a new period.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Period $period)
    {
        abort_unless(\Gate::allows('period_edit'), 403);

        $organizations = auth()->user()->organizations()->get();
        return view('periods.edit')
                    ->with(compact('period'))
                    ->with(compact('organizations'));
    }
    
    public function update(Period $period)
    {
        abort_unless(\Gate::allows('period_edit'), 403);
        $new_period = $this->validateRequest();
        $period->update([
            'title' => $new_period['title'],
            'begin' => $new_period['begin'],
            'end' => $new_period['end'],
            'organization_id' => format_select_input($new_period['organization_id']),
            'owner_id' => auth()->user()->id
        ]);

        return redirect()->route('periods.index');
    }
    
    /**
     * Remove the specified period from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Period $period)
    {
        abort_unless(\Gate::allows('period_delete'), 403);

        $period->delete();

        return back();
    }
    
    /**
     * Display the specified period.
     *
     * @param int $id
     *
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
            'organization_id'   => 'sometimes',
            'owner_id'          => 'sometimes',
        ]);
    }
    
}
