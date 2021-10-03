<?php

namespace App\Http\Controllers;

use App\RepositorySubscription;
use Illuminate\Http\Request;

//use DonatelloZa\RakePlus\RakePlus;

class RepositorySubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $input = $this->validateRequest();
        $subscriptions = RepositorySubscription::where('owner_id', auth()->user()->id)
                ->where('subscribable_type', $input['subscribable_type'])
                ->where('subscribable_id', $input['subscribable_id'])
                ->where('repository', $input['repository'])->get();
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

        $repositoryPlugin = app()->make('App\Plugins\Repositories\RepositoryPlugin');
        if (request()->wantsJson()){
            return ['subscription' => $repositoryPlugin->plugins[$input['repository']]->store($request)];
        }
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
        $repositoryPlugin = app()->make('App\Plugins\Repositories\RepositoryPlugin');
        $result = collect([]);
        /* not used anymore */
        /*$subscriptions = RepositorySubscription::where('subscribable_type', $input['subscribable_type'])
                ->where('subscribable_id', $input['subscribable_id'])
                ->where('repository', $input['repository'])->get();

        foreach($subscriptions as $subscription)
        {
            $result->push($repositoryPlugin->plugins[$input['repository']]->processReference($subscription->value));
        }*/

        /*
         * Get media by subscribable identifier if (curriculum, terminal, or enabling objective)
         */
        $allowed_models = array("App\Curriculum", "App\TerminalObjective", "App\EnablingObjective");
        if (in_array($input['subscribable_type'], $allowed_models)) {
            $model = $input['subscribable_type']::find($input['subscribable_id']);

            if (!empty($model->ui))
            {
                $result->push($repositoryPlugin->plugins[$input['repository']]->processReference('endpoint=getSearchQueriesV2&property=ccm:curriculum&value='.$model->ui.'&maxItems='.$input['maxItems'].'&skipCount='.($input['maxItems'] * $input['page']).'&filter='.$input['filter'] ));
                LogController::set(get_class($this).'@'.__FUNCTION__, $model->uuid, $result->count());
            }
            else
            {
                //dump('ui: '.$model->ui);
                $result = null;
            }
        }

        if (request()->wantsJson()){
            return ['message' => $result];
        }
    }


    public function searchRepository(Request $request)
    {
        $input = $this->validateRequest();

        $repositoryPlugin = app()->make('App\Plugins\Repositories\RepositoryPlugin');
        return $repositoryPlugin->plugins[$input['repository']]->searchRepository($request);
    }

    protected function callPlugin($plugin, $value)
    {
        $repositoryPlugin = app()->make('App\Plugins\Repositories\RepositoryPlugin');
        return $repositoryPlugin->plugins[$plugin]->processReference($value);
    }

    public function destroySubscription(Request $request)
    {
        $input = $this->validateRequest();

        $repositoryPlugin = app()->make('App\Plugins\Repositories\RepositoryPlugin');
        return $repositoryPlugin->plugins[$input['repository']]->destroy($request);
    }


    protected function validateRequest()
    {

        return request()->validate([
            'value'             => 'sometimes',
            'subscribable_type' => 'sometimes|required',
            'subscribable_id'   => 'sometimes|required',
            'search'            => 'sometimes',
            'page'              => 'sometimes',
            'maxItems'          => 'sometimes',
            'repository'        => 'sometimes',
            'filter'            => 'sometimes',
        ]);
    }

}
