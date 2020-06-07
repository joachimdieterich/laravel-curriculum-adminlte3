<?php

namespace App\Http\Controllers;

use App\Absence;
use Illuminate\Http\Request;

class AbsenceController extends Controller
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
        abort_unless(\Gate::allows('absence_create'), 403);
        $new_absence = $this->validateRequest();
        
        $absence = Absence::updateOrCreate(
            [
                'referenceable_type' => $new_absence['referenceable_type'],
                'referenceable_id'   => $new_absence['referenceable_id'],
                'absent_user_id'     => $new_absence['absent_user_id'],
            ],
            [
                'reason'             => $new_absence['reason'],
                'done'               => isset($new_absence['done']) ? $new_absence['done'] : 0, 
                'owner_id'           => auth()->user()->id
            ]    
        );
        
        // axios call? 
        if (request()->wantsJson()){    
            return ['message' => $absence];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Absence  $absence
     * @return \Illuminate\Http\Response
     */
    public function show(Absence $absence)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Absence  $absence
     * @return \Illuminate\Http\Response
     */
    public function edit(Absence $absence)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Absence  $absence
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Absence $absence)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Absence  $absence
     * @return \Illuminate\Http\Response
     */
    public function destroy(Absence $absence)
    {
        //
   
        
    }
    
    protected function validateRequest()
    {   
        
        return request()->validate([
            'id'                 => 'sometimes',
            'reason'             => 'sometimes|required',
            'absent_user_id'     => 'sometimes',
            'done'               => 'sometimes',
            'owner_id'           => 'sometimes',
            'referenceable_type' => 'sometimes',
            'referenceable_id'   => 'sometimes',
        ]);
    }
    
}
