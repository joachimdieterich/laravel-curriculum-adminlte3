<?php

namespace App\Http\Controllers;

use App\TerminalObjectiveSubscriptions;
use Illuminate\Http\Request;

class TerminalObjectiveSubscriptionsController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $new_subscription= $this->validateRequest();
        $subscription = TerminalObjectiveSubscriptions::firstOrCreate([
            'terminal_objective_id' => $new_subscription['terminal_objective_id'],
            'subscribable_type'     => $new_subscription['subscribable_type'],
            'subscribable_id'       => $new_subscription['subscribable_id'],
            'sharing_level_id'      => 1,
            'visibility'            => true,
            'owner_id'              => auth()->user()->id,
        ]);
        if (request()->wantsJson()){
            return ['message' => 'ok'];
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            "terminal_objective_id" => 'sometimes|required',
            "subscribable_type" => 'required',
            "subscribable_id" => 'required',
        ]);
    }
}
