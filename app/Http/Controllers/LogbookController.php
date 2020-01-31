<?php

namespace App\Http\Controllers;

use App\Logbook;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class LogbookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $logbooks = Logbook::all();
        return view('logbooks.index')
            ->with(compact('logbooks'));
    }
    
    public function list()
    {
        
        abort_unless(\Gate::allows('logbook_access'), 403);
        $logbooks = Logbook::select([
            'id', 
            'title',
            ]);
        
        return DataTables::of($logbooks)
            ->addColumn('action', function ($logbooks) {
                 $actions  = '';
                    if (\Gate::allows('logbook_show')){
                        $actions .= '<a href="'.route('logbooks.show', $logbooks->id).'" '
                                    . 'id="show-logbook-'.$logbooks->id.'" '
                                    . 'class="btn btn-xs btn-success mr-1">'
                                    . '<i class="fa fa-list-alt"></i>'
                                    . '</a>';
                    }
                    if (\Gate::allows('logbook_edit')){
                        $actions .= '<a href="'.route('logbooks.edit', $logbooks->id).'" '
                                    . 'id="edit-logbook-'.$logbooks->id.'" '
                                    . 'class="btn btn-xs btn-primary mr-1">'
                                    . '<i class="fa fa-edit"></i>'
                                    . '</a>';
                    }
                    if (\Gate::allows('logbook_delete')){
                        $actions .= '<form action="'.route('logbooks.destroy', $logbooks->id).'" method="POST" class="float-right">'
                                    . '<input type="hidden" name="_method" value="delete">'. csrf_field().''
                                    . '<button '
                                    . 'type="submit" '
                                    . 'id="delete-logbook-'.$logbooks->id.'" '
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_unless(\Gate::allows('logbook_create'), 403);
        
        $logbooks = Logbook::all();
        return view('logbooks.create')
                ->with(compact('logbooks'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('logbook_create'), 403);
        $new_logbook = $this->validateRequest();
        
        $logbook = Logbook::firstOrCreate([
            'title'         => $new_logbook['title'],
            'description'   => $new_logbook['description'],
            'owner_id'      => auth()->user()->id,
        ]);
        
        // axios call? 
        if (request()->wantsJson()){    
            return ['message' => $logbook->path()];
        }
        
        return redirect($logbook->path());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Logbook  $logbook
     * @return \Illuminate\Http\Response
     */
    public function show(Logbook $logbook)
    {           
        $logbook = $logbook->with(['entries.contentSubscriptions.content.categories', 
                                   'entries.terminalObjectiveSubscriptions.terminalObjective',
                                   'entries.enablingObjectiveSubscriptions.enablingObjective.terminalObjective',])->get()->first();
        
        return view('logbooks.show')
                ->with(compact('logbook'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Logbook  $logbook
     * @return \Illuminate\Http\Response
     */
    public function edit(Logbook $logbook)
    {
        abort_unless(\Gate::allows('logbook_edit'), 403);
        
        return view('logbooks.edit')
                ->with(compact('logbook'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Logbook  $logbook
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Logbook $logbook)
    {
        abort_unless(\Gate::allows('logbook_edit'), 403);
        
        $logbook->update([
            'title' => $request['title'],
            'description' => $request['description'],
        ]);

        return redirect()->route('logbooks.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Logbook  $logbook
     * @return \Illuminate\Http\Response
     */
    public function destroy(Logbook $logbook)
    {
        abort_unless(\Gate::allows('logbook_delete'), 403);

        $logbook->delete();

        return back();
    }
    
    protected function validateRequest()
    {   
        
        return request()->validate([
            'title'         => 'sometimes|required',
            'description'   => 'sometimes',
        ]);
    }
}
