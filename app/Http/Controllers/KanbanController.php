<?php

namespace App\Http\Controllers;

use App\Kanban;
use App\Medium;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class KanbanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_unless(\Gate::allows('kanban_access'), 403);

        return view('kanbans.index');
    }
    
    public function list()
    {
        
        abort_unless(\Gate::allows('kanban_access'), 403);
        $kanbans = (auth()->user()->role()->id == 1) ? Kanban::all() : auth()->user()->kanbans()->get();      
   
        return empty($kanbans) ? '' : DataTables::of($kanbans)
            ->addColumn('action', function ($kanbans) {
                 $actions  = '';
                    if (\Gate::allows('kanban_edit')){
                        $actions .= '<a href="'.route('kanbans.edit', $kanbans->id).'" '
                                    . 'id="edit-kanban-'.$kanbans->id.'" '
                                    . 'class="px-2 text-black">'
                                    . '<i class="fa fa-pencil-alt"></i>'
                                    . '</a>';
                    }
                    if (\Gate::allows('kanban_delete')){
                        $actions .= '<button type="button" class="btn text-danger" onclick="event.preventDefault();destroyDataTableEntry(\'kanbans\','.$kanbans->id.');"><i class="fa fa-trash"></i></button>';
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
        abort_unless(\Gate::allows('kanban_create'), 403);
        
        return view('kanbans.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('kanban_create'), 403);
        $new_kanban = $this->validateRequest();
        
        $kanban = Kanban::Create([
            'title'         => $new_kanban['title'],
            'description'   => $new_kanban['description'],
            'medium_id'     => $this->getMediumIdByInputFilepath($new_kanban),
            'owner_id'      => auth()->user()->id,
        ]);
        
        // axios call? 
        if (request()->wantsJson()){    
            return ['message' => $kanban->path()];
        }
        
        return redirect($kanban->path());
    }
    
    /**
     * If $input['filepath'] is set and medium exists, id is return, else return is null
     * @param array $input
     * @return mixed
     */
    public function getMediumIdByInputFilepath($input){
        if (isset($input['filepath']))
        {
            $medium = new Medium();
            return (null !== $medium->getByFilemanagerPath($input['filepath'])) ? $medium->getByFilemanagerPath($input['filepath'])->id : null;
        } 
        else
        {
            return null;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Kanban  $kanban
     * @return \Illuminate\Http\Response
     */
    public function show(Kanban $kanban)
    {
        $kanban   = $kanban->with(['statuses', 'statuses.items' => function($query) use ($kanban) {
                    $query->where('kanban_id', $kanban->id)->with(['owner', 'taskSubscription.task.subscriptions' => function($query) {
                         $query->where('subscribable_id', auth()->user()->id)
                               ->where('subscribable_type', 'App\User');
                 }, 'mediaSubscriptions', 'media'])->orderBy('order_id');
                    }, 'statuses.items.subscribable'])->where('id', $kanban->id)->get()->first();
       
        return view('kanbans.show')
                ->with(compact('kanban'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kanban  $kanban
     * @return \Illuminate\Http\Response
     */
    public function edit(Kanban $kanban)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Kanban  $kanban
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Kanban $kanban)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kanban  $kanban
     * @return \Illuminate\Http\Response
     */
    public function destroy(Kanban $kanban)
    {
        abort_unless(\Gate::allows('kanban_delete'), 403);

        //delete relations
        $kanban->items()->delete();
        $kanban->statuses()->delete();
        $kanban->subscriptions()->delete();

        $kanban->delete();

    }
    
    protected function validateRequest()
    {   
        
        return request()->validate([
            'title'         => 'sometimes|required',
            'description'   => 'sometimes',
            'filepath'      => 'sometimes',
        ]);
    }
}
