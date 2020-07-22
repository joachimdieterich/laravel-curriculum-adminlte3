<?php

namespace App\Http\Controllers;

use App\EventSubscription;
use App\EventmanagementPlugin;
use Illuminate\Http\Request;

class EventSubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EventSubscription  $eventSubscription
     * @return \Illuminate\Http\Response
     */
    public function show(EventSubscription $eventSubscription)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EventSubscription  $eventSubscription
     * @return \Illuminate\Http\Response
     */
    public function edit(EventSubscription $eventSubscription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EventSubscription  $eventSubscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EventSubscription $eventSubscription)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EventSubscription  $eventSubscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(EventSubscription $eventSubscription)
    {
        //
    }
    
    public function getEvents(Request $request)
    {
        $input = $this->validateRequest();
        
        
//        $subscriptions = RepositorySubscription::where('subscribable_type', $input['subscribable_type'])
//                ->where('subscribable_id', $input['subscribable_id'])
//                ->where('repository', $input['repository'])->get();
//        
//        $result = collect([]);
//        foreach($subscriptions as $subscription)
//        {
//            $result->push($this->callPlugin($input['repository'], $subscription));
//        }
                
        $vm = new EventmanagementPlugin();
        $events = $vm->plugins[env('EVENTMANAGEMENTPLUGIN')]->lesePlrlpVeranstaltungen(['search'=> $input['search'], 'page' => $input['page']]);
        
        if (request()->wantsJson()){    
            return ['message' => $events];
        }
    }
    
    protected function validateRequest()
    {               
        
        return request()->validate([
            'value'             => 'sometimes',
            'subscribable_type' => 'sometimes|required',
            'subscribable_id'   => 'sometimes|required',
            'search'            => 'sometimes',
            'page'            => 'sometimes',
            'plugin'            => 'required',
        ]);
    }
}
