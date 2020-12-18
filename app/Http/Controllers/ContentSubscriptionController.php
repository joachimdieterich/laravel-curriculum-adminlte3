<?php

namespace App\Http\Controllers;

use App\ContentSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContentSubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $input = $this->validateRequest();

        $subscriptions = ContentSubscription::where([
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id'   => $input['subscribable_id']
        ])->orderBy('order_id');


        if (request()->wantsJson()){
            return ['message' => $subscriptions->with(['content'])->get()];
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ContentSubscription  $contentSubscription
     * @return \Illuminate\Http\Response
     */
    public function show(ContentSubscription $contentSubscription)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ContentSubscription  $contentSubscription
     * @return \Illuminate\Http\Response
     */
    public function edit(ContentSubscription $contentSubscription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //first get existing data to later adjust order_id
        $subscription_request = $this->validateRequest();
        $old_subscription = ContentSubscription::where('content_id', $subscription_request['content_id'])
            ->where('subscribable_type', $subscription_request['subscribable_type'])
            ->where('subscribable_id', $subscription_request['subscribable_id'])
            ->get()->first();
        // update order_id
        if ($request->has('order_id')){

            if ($this->toggleOrderId($old_subscription, request('order_id'))){
                $subscriptions = ContentSubscription::where([
                    'subscribable_type' => $subscription_request['subscribable_type'],
                    'subscribable_id'   => $subscription_request['subscribable_id']
                ])->orderBy('order_id');


                if (request()->wantsJson()){
                    return ['message' => $subscriptions->with(['content'])->get()];
                }
            }


        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ContentSubscription  $contentSubscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(ContentSubscription $contentSubscription)
    {
        //
    }

    protected function getMaxOrderId($subscribable_type, $subscribable_id)
    {

        $order_id = DB::table('content_subscriptions')
            ->where('subscribable_type', $subscribable_type)
            ->where('subscribable_id', $subscribable_id)
            ->max('order_id');

        return (is_numeric($order_id)) ? $order_id + 1 : 0 ;
    }

    protected function resetOrderIds( $subscribable_type, $subscribable_id, $order_id, $direction = 'down')
    {
        return (new ContentSubscription)
            ->where('subscribable_type', $subscribable_type)
            ->where('subscribable_id', $subscribable_id)
            ->where('order_id', '>', $order_id)
            ->update([
                'order_id'=> DB::raw('order_id'. ( ($direction === 'down') ? '-1' : '+1') )
            ]);
    }

    protected function toggleOrderId($old_subscription, $new_order_id)
    {
        //fix for old subscriptions without order_id (multiple entries with 0)
        $check_subscriptions = (new ContentSubscription)
            ->where('subscribable_type', $old_subscription->subscribable_type)
            ->where('subscribable_id', $old_subscription->subscribable_id)
            ->where('order_id', '=', 0)->get();


        if ($check_subscriptions->count() > 1)
        {
            $i = 0;
            foreach ($check_subscriptions AS $subscription)
            {
                DB::table('content_subscriptions')
                ->where('content_id', $subscription->content_id)
                ->where('subscribable_type', $subscription->subscribable_type)
                ->where('subscribable_id', $subscription->subscribable_id)
                ->where('order_id', '=', $subscription->order_id)
                ->update(['order_id' => $i]);
                $i++;
            }
        }

        // toggle order_ids of ContentSubscriptions
        $responseA = (new ContentSubscription)
            ->where('subscribable_type', $old_subscription->subscribable_type)
            ->where('subscribable_id', $old_subscription->subscribable_id)
            ->where('order_id', '=', $new_order_id)
            ->update([ 'order_id' => $old_subscription->order_id ]);

        $responseB = (new ContentSubscription)
            ->where('content_id', $old_subscription->content_id)
            ->where('subscribable_type', $old_subscription->subscribable_type)
            ->where('subscribable_id', $old_subscription->subscribable_id)
            ->update([ 'order_id'=> $new_order_id]);

        if (($responseA == true) AND ($responseB == true))
        {
            if (request()->wantsJson()){
                return ['message' => true];
            }
        }

    }

    protected function validateRequest()
    {
        return request()->validate([
            'content_id'        => 'sometimes',
            'subscribable_type' => 'required',
            'subscribable_id'   => 'required',
            'sharing_level_id'  => 'sometimes',
            'order_id'          => 'sometimes',
            'visibility'        => 'sometimes',
        ]);
    }
}
