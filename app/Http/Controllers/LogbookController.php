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
        return view('logbooks.index');
    }
    
    public function list()
    {
        
        abort_unless(\Gate::allows('logbook_access'), 403);
        $logbooks = (auth()->user()->role()->id == 1) ? Logbook::all() : auth()->user()->logbooks()->get();      
   
        return empty($logbooks) ? '' : DataTables::of($logbooks)
            ->addColumn('action', function ($logbooks) {
                 $actions  = '';
                    if (\Gate::allows('logbook_edit')){
                        $actions .= '<a href="'.route('logbooks.edit', $logbooks->id).'" '
                                    . 'id="edit-logbook-'.$logbooks->id.'" '
                                    . 'class="px-2 text-black">'
                                    . '<i class="fa fa-pencil-alt"></i>'
                                    . '</a>';
                    }
                    if (\Gate::allows('logbook_delete')){
                        $actions .= '<button type="button" class="btn text-danger" onclick="event.preventDefault();destroyDataTableEntry(\'logbooks\','.$logbooks->id.');"><i class="fa fa-trash"></i></button>';
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
        
        $logbook = Logbook::Create([
            'title'         => $new_logbook['title'],
            'description'   => $new_logbook['description'],
            'owner_id'      => auth()->user()->id,
        ]);
        
        //subscribe to model
        if (isset($new_logbook['subscribable_type']) AND isset($new_logbook['subscribable_id'])){
            $model = $new_logbook['subscribable_type']::find($new_logbook['subscribable_id']);
            $logbook->subscribe($model);
        }
        
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
                                   'entries.enablingObjectiveSubscriptions.enablingObjective.terminalObjective',
                                   'entries.taskSubscription.task.subscriptions' => function($query) {
                                        $query->where('subscribable_id', auth()->user()->id)
                                              ->where('subscribable_type', 'App\User');
                                    },
                                    'entries.mediaSubscriptions.medium'
            ])->where('id', $logbook->id)->get()->first();
        
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

        //return back();
    }
    
    protected function validateRequest()
    {   
        
        return request()->validate([
            'title'         => 'sometimes|required',
            'description'   => 'sometimes',
            'subscribable_type' => 'sometimes',
            'subscribable_id'   => 'sometimes',
        ]);
    }
}
