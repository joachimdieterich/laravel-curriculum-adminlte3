<?php

namespace App\Http\Controllers;

use App\MediumSubscription;
use App\Artefact;
use Illuminate\Http\Request;

class ArtefactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_unless(\Gate::allows('artefact_access'), 403);
        $input = $this->validateRequest();

        $subscriptions = Artefact::where([
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id'   => $input['subscribable_id'],
            'user_id'   => auth()->user()->id
        ]);
        //dump($subscriptions->get());

        if (request()->wantsJson()){
            return ['message' => $subscriptions->with(['medium'])->get()];
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
        abort_unless(\Gate::allows('artefact_create'), 403);

        $new_subscription = $this->validateRequest();

        $subscribe = Artefact::firstOrCreate([
            "medium_id" =>  $new_subscription['medium_id'],
            "subscribable_type"=> $new_subscription['subscribable_type'],
            "subscribable_id"=> $new_subscription['subscribable_id'],
            "user_id"=> auth()->user()->id,
        ]);
        if (request()->wantsJson()){
            return ['message' => $subscribe->save()];
        }

    }

     public function destroySubscription(Request $request)
    {
        abort_unless(\Gate::allows('artefact_delete'), 403);
        $subscription = $this->validateRequest();

        return Artefact::where([
            "medium_id" =>  $subscription['medium_id'],
            "subscribable_type"=> $subscription['subscribable_type'],
            "subscribable_id"=> $subscription['subscribable_id'],
            "user_id"=> auth()->user()->id,
        ])->delete();

    }

    protected function validateRequest()
    {
        return request()->validate([
            'medium_id'         => 'sometimes',
            'subscribable_type' => 'required',
            'subscribable_id'   => 'required',
        ]);
    }
}
