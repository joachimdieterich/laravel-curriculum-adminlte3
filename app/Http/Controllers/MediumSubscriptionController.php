<?php

namespace App\Http\Controllers;

use App\Medium;
use App\MediumSubscription;
use Illuminate\Http\Request;

class MediumSubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $input = $this->validateRequest();
        //dump($input);
        $subscriptions = MediumSubscription::where([
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id' => $input['subscribable_id'],
        ]);
        //dump($subscriptions->get());

        if (request()->wantsJson()) {
            return ['message' => $subscriptions->with(['medium'])->get()];
        }
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
        abort_unless(\Gate::allows('medium_create'), 403);
        $input = $this->validateRequest();

        $subscribe = MediumSubscription::updateOrCreate([
            'medium_id' => $input['medium_id'],
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id' => $input['subscribable_id'],
        ], [
            'sharing_level_id' => $input['sharing_level_id'] ?? 1,
            'visibility' => $input['visibility'] ?? 1,
            'owner_id' => auth()->user()->id,
        ]);
        $subscribe->save();

        return $subscribe;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\MediumSubscription  $mediumSubscription
     * @return \Illuminate\Http\Response
     */
    public function show(MediumSubscription $mediumSubscription)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MediumSubscription  $mediumSubscription
     * @return \Illuminate\Http\Response
     */
    public function edit(MediumSubscription $mediumSubscription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MediumSubscription  $mediumSubscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MediumSubscription $mediumSubscription)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MediumSubscription  $mediumSubscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(MediumSubscription $mediumSubscription)
    {
    }

    public function destroySubscription(Request $request)
    {
        abort_unless(\Gate::allows('medium_delete'), 403);
        $subscription = $this->validateRequest();
        $model = $subscription['subscribable_type'];
        //! CHANGE IN NEW VERSION
        // temporary solution to remove mediaSubscriptions for Logbooks/Kanbans
        if ($model == 'App\\Logbook' || $model == 'App\\Kanban') {
            app($model)::find($subscription['subscribable_id'])->update(['medium_id' => null]);
        }

        return MediumSubscription::where([
            'medium_id' => $subscription['medium_id'],
            'subscribable_type' => $subscription['subscribable_type'],
            'subscribable_id' => $subscription['subscribable_id'],
            'sharing_level_id' => $subscription['sharing_level_id'],
            'visibility' => $subscription['visibility'],
            //"owner_id"=> auth()->user()->id, //Todo: admin should be able to delete everything
        ])->delete();
    }

    protected function validateRequest()
    {
        return request()->validate([
            'path' => 'sometimes',
            'medium_id' => 'sometimes',
            'subscribable_type' => 'required',
            'subscribable_id' => 'required',
            'sharing_level_id' => 'sometimes',
            'visibility' => 'sometimes',
            'additional_data' => 'sometimes',
        ]);
    }
}
