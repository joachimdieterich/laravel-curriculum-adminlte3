<?php

namespace App\Http\Controllers;

use App\ReferenceSubscription;
use Illuminate\Http\Request;

class ReferenceSubscriptionController extends Controller
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
     * @param  \App\ReferenceSubscription  $referenceSubscription
     * @return \Illuminate\Http\Response
     */
    public function show(ReferenceSubscription $referenceSubscription)
    {
       
        $reference_subscription = ReferenceSubscription::where('reference_id', $referenceSubscription->reference_id)
                        ->with(['siblings' => function($query) use ($referenceSubscription) {
                                $query->where('reference_id', $referenceSubscription->reference_id)
                                ->where('referenceable_id', '!=', $referenceSubscription->referenceable_id)
                                ->where('referenceable_type', '=', $referenceSubscription->referenceable_type);
                            }])->get();
//      
        dd($reference_subscription);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ReferenceSubscription  $referenceSubscription
     * @return \Illuminate\Http\Response
     */
    public function edit(ReferenceSubscription $referenceSubscription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ReferenceSubscription  $referenceSubscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ReferenceSubscription $referenceSubscription)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ReferenceSubscription  $referenceSubscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(ReferenceSubscription $referenceSubscription)
    {
        //
    }
    
   /**
     * Display the specified resource.
     *
     * @param  \App\ReferenceSubscription  $referenceSubscription
     * @return \Illuminate\Http\Response
     */
    public function siblings(ReferenceSubscription $referenceSubscription)
    {
       dd($referenceSubscription);
        return ReferenceSubscription::where('reference_id', $referenceSubscription->reference_id)
                        ->with(['siblings' => function($query) use ($referenceSubscription) {
                                $query->where('reference_id', $referenceSubscription->reference_id)
                                ->where('referenceable_id', '!=', $referenceSubscription->referenceable_id)
                                ->where('referenceable_type', '=', $referenceSubscription->referenceable_type);
                            }])->get();

    }
}
