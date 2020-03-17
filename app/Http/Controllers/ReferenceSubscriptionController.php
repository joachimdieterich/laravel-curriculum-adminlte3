<?php

namespace App\Http\Controllers;

use App\ReferenceSubscription;
use App\Reference;
use App\Curriculum;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        $new_subscription   = $this->validateRequest();
        $uuid               = Str::uuid();
        if (($new_subscription['subscribable_type'] ==  "App\TerminalObjective" AND $new_subscription['terminal_objective_id'] == $new_subscription['subscribable_id'])
            OR
            ($new_subscription['subscribable_type'] ==  "App\EnablingObjective" AND $new_subscription['enabling_objective_id'] == $new_subscription['subscribable_id'])
           )
        {
            return false;
        }
        
        $reference = Reference::Create([
            'id'          => (string) Str::uuid(),
            'description' => '',
            'grade_id'    => isset($new_subscription['grade_id']) ? $new_subscription['grade_id'] : Curriculum::find($new_subscription['curriculum_id'])->grade_id,
            'owner_id'    => auth()->user()->id,	
        ]);
        $subscription = ReferenceSubscription::Create([
            'reference_id'       => $reference->id,
            'referenceable_type' => $new_subscription['subscribable_type'],
            'referenceable_id'   => $new_subscription['subscribable_id'],
            'sharing_level_id'   => isset($new_subscription['sharing_level_id']) ? $new_subscription['sharing_level_id'] : 1,
            'visibility'         => isset($new_subscription['visibility']) ? $new_subscription['visibility'] : true,
            'owner_id'           => auth()->user()->id,	
        ]);
        
        if ($subscription){ //generate sibling
            $subscription = ReferenceSubscription::Create([
                'reference_id'       => $reference->id,
                'referenceable_type' => ($new_subscription['enabling_objective_id'] != null) ? "App\EnablingObjective" : "App\TerminalObjective",
                'referenceable_id'   => ($new_subscription['enabling_objective_id'] != null)  ? $new_subscription['enabling_objective_id'] : $new_subscription['terminal_objective_id'],
                'sharing_level_id'   => isset($new_subscription['sharing_level_id']) ? $new_subscription['sharing_level_id'] : 1,
                'visibility'         => isset($new_subscription['visibility']) ? $new_subscription['visibility'] : true,
                'owner_id'           => auth()->user()->id,	
            ]);
        }
        if (request()->wantsJson()){    
            return ['message' => 'ok'];
        }
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
       //dd($referenceSubscription);
        return ReferenceSubscription::where('reference_id', $referenceSubscription->reference_id)
                        ->with(['siblings' => function($query) use ($referenceSubscription) {
                                $query->where('reference_id', $referenceSubscription->reference_id)
                                ->where('referenceable_id', '!=', $referenceSubscription->referenceable_id)
                                ->where('referenceable_type', '=', $referenceSubscription->referenceable_type);
                            }])->get();

    }
    
    protected function validateRequest()
    {   
        return request()->validate([
            "curriculum_id" => 'sometimes|required',
            "grade_id" => 'sometimes',
            "terminal_objective_id" => 'sometimes|required',
            "enabling_objective_id" => 'sometimes',
            "subscribable_type" => 'required',
            "subscribable_id" => 'required',
        ]);
    }
}
