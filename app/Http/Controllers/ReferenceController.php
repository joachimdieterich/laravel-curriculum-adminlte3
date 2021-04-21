<?php

namespace App\Http\Controllers;

use App\Reference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferenceController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         abort_unless(\Gate::allows('objective_create'), 403);
         return Reference::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Reference  $reference
     * @return \Illuminate\Http\Response
     */
    public function show(Reference $reference)
    {
        if (request()->wantsJson()){
            return [
                'reference' => $reference
            ];
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Reference  $reference
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reference $reference)
    {
        abort_unless(\Gate::allows('objective_edit'), 403);
        if (request()->wantsJson()){
            return ['message' => $reference->update($request->all())];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Reference  $reference
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reference $reference)
    {
        abort_unless(\Gate::allows('objective_delete'), 403);
        DB::table('reference_subscriptions')
            ->where('reference_id',  $reference->id)
            ->delete(); //delete individual subscriptions
        $reference->delete();
        if (request()->wantsJson()){
            return ['message' =>'deleted'];
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'id'                => 'sometimes',
            'description'       => 'sometimes',
            'grade_id'          => 'sometimes',
            'owner_id'          => 'sometimes',
            ]);
    }
}
