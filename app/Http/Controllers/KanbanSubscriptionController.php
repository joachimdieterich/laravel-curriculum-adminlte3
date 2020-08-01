<?php

namespace App\Http\Controllers;

use App\Kanban;
use App\KanbanSubscription;
use Illuminate\Http\Request;

class KanbanSubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // axios call? 
        if (request()->wantsJson()){    
            return ['subscribers' => [
                'users' =>  auth()->user()->users(),
                'groups' => auth()->user()->groups()->select('group_id','title')->get(),
                'organizations' => auth()->user()->organizations()->select('organization_id','title')->get(),
                'subscriptions' => Kanban::find(request('kanban_id'))->subscriptions()->with('subscribable')->get()
            ]];
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $this->validateRequest();
        
        $subscribe = KanbanSubscription::updateOrCreate([
                "kanban_id"         => $input['model_id'],
                "subscribable_type" => $input['subscribable_type'],
                "subscribable_id"   => $input['subscribable_id'],
            ],[
                "editable"=> isset($input['editable']) ? $input['editable'] : false,
                "owner_id"=> auth()->user()->id,
            ]);
        $subscribe->save();
        
        // axios call? 
        if (request()->wantsJson()){    
            return ['subscription' => Kanban::find($input['model_id'])->subscriptions()->with('subscribable')->get()];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\KanbanSubscription  $kanbanSubscription
     * @return \Illuminate\Http\Response
     */
    public function show(KanbanSubscription $kanbanSubscription)
    {
        //
    }

    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\KanbanSubscription  $kanbanSubscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, KanbanSubscription $kanbanSubscription)
    {
        $input = $this->validateRequest();
        
        $kanbanSubscription->update([
            "editable"=> isset($input['editable']) ? $input['editable'] : false,
            "owner_id"=> auth()->user()->id,
        ]);
        
        // axios call? 
        if (request()->wantsJson()){    
            return ['editable' => $kanbanSubscription->editable];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\KanbanSubscription  $kanbanSubscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(KanbanSubscription $kanbanSubscription)
    {
        abort_unless(\Gate::allows('kanban_delete'), 403);
        // axios call? 
        if (request()->wantsJson()){    
            return ['message' => $kanbanSubscription->delete()];
        }
        
    }
    
    protected function validateRequest()
    {               
        return request()->validate([
            'subscribable_type' => 'sometimes|string',
            'subscribable_id'   => 'sometimes|integer',
            'model_id'          => 'sometimes|integer',
            'editable'          => 'sometimes',
        ]);
    }
}
