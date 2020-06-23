<?php

namespace App\Http\Controllers;

use App\RepositorySubscription;
use Illuminate\Http\Request;

use DonatelloZa\RakePlus\RakePlus;

class RepositorySubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $subscriptions = RepositorySubscription::where('owner_id', auth()->user()->id)->get();
        return ['subscriptions' => $subscriptions];
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
        $input = $this->validateRequest();
        
        $repositoryPlugin = app()->make('App\RepositoryPlugin');
        return $repositoryPlugin->plugins[$input['repository']]->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RepositorySubscription  $repositorySubscription
     * @return \Illuminate\Http\Response
     */
    public function show(RepositorySubscription $repositorySubscription)
    {   
      
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RepositorySubscription  $repositorySubscription
     * @return \Illuminate\Http\Response
     */
    public function edit(RepositorySubscription $repositorySubscription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RepositorySubscription  $repositorySubscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RepositorySubscription $repositorySubscription)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RepositorySubscription  $repositorySubscription
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
       
    }
    
    public function getMedia(Request $request)
    {
        $input = $this->validateRequest();
        
        $subscriptions = RepositorySubscription::where('subscribable_type', $input['subscribable_type'])
                ->where('subscribable_id', $input['subscribable_id'])
                ->where('repository', $input['repository'])->get();
        
        $result = collect([]);
        foreach($subscriptions as $subscription)
        {
            $result->push($this->callPlugin($input['repository'], $subscription->value));
        }
        if (isset($input['search']))
        {  
            $rake = RakePlus::create(strip_tags($input['search']), 'de_DE', 3);
            $phrase_scores = $rake->sort('asc')->get(); 
            $repositoryPlugin = app()->make('App\RepositoryPlugin');
            
            $result->push($repositoryPlugin->plugins[$input['repository']]->searchRepository($phrase_scores[0]));
            
        }
        
        if (request()->wantsJson()){    
            return ['message' => $result];
        }
    }
    
    public function searchRepository(Request $request)
    {
        $input = $this->validateRequest();
        
        $repositoryPlugin = app()->make('App\RepositoryPlugin');
        return $repositoryPlugin->plugins[$input['repository']]->searchRepository($request);
    }
    
    protected function callPlugin($plugin, $value)
    {
        $repositoryPlugin = app()->make('App\RepositoryPlugin');
        return $repositoryPlugin->plugins[$plugin]->processReference($value);
    }
    
    public function destroySubscription(Request $request)
    {
        $input = $this->validateRequest();
        
        $repositoryPlugin = app()->make('App\RepositoryPlugin');
        return $repositoryPlugin->plugins[$input['repository']]->destroy($request);
    }


    protected function validateRequest()
    {               
        
        return request()->validate([
            'value' => 'sometimes',
            'subscribable_type' => 'sometimes|required',
            'subscribable_id'   => 'sometimes|required',
            'search'            => 'sometimes',
            'repository'        => 'required',
        ]);
    }
    
    
}
