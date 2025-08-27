<?php

namespace App\Http\Controllers;

use App\Medium;
use App\MediumSubscription;
use Illuminate\Http\Request;
use App\Plugins\Repositories\edusharing\Edusharing;

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

        return MediumSubscription::where([
            'medium_id' => $input['medium_id'],
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id' => $input['subscribable_id'],
        ])->with('medium')->first();
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
        $input = $this->validateRequest();

        // if request didn't send additional_data (on models where only 1 medium can be attached)
        if (!isset($input['additional_data'])) {
            // remove the medium_id from the model, so deleting the medium doesn't cause a foreign key constraint error
            $input['subscribable_type']::where('id', $input['subscribable_id'])->update(['medium_id' => null]);
        }

        // technically there could be more than 1 result, but in practice a model can only have 1 attached medium
        // also, for each edusharing-subscription a new medium is created, so these should always be unique (1:1)
        $query = MediumSubscription::where([
            'medium_id' => $input['medium_id'],
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id' => $input['subscribable_id'],
            'sharing_level_id' => $input['sharing_level_id'],
            'visibility' => $input['visibility'],
        ]);

        $subscription = $query->first();

        // if subscription doesn't exist => curriculum-media attached to a model->medium_id
        if (isset($subscription)) {
            // additional-data is needed to delete the usage
            $additional_data = $subscription->additional_data;
            // subscription needs to be deleted before deleting the medium
            $query->delete();

            if (isset($additional_data)) {
                // delete the usage
                $edusharing = new Edusharing;
                $edusharing->deleteUsage(
                    $additional_data['nodeId'],
                    $additional_data['usageId'],
                );
                // and then delete the medium-entry
                Medium::find($input['medium_id'])->delete();
            }
        }

        return true;
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
