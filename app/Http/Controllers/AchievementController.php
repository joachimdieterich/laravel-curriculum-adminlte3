<?php

namespace App\Http\Controllers;

use App\Achievement;
use Illuminate\Http\Request;

class AchievementController extends Controller
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
        abort_unless((\Gate::allows('achievement_create') OR \Gate::allows('achievement_self_assessment')), 403);
        
        $input = $this->validateRequest();
       
        $user_ids = !empty($input['user_id']) ? $input['user_id']: auth()->user()->id;
        
        foreach((array) $user_ids AS $user_id)
        {
            $achievement = Achievement::where('referenceable_type', '=', $input['referenceable_type'])
                                      ->where('referenceable_id', '=', $input['referenceable_id'])
                                      ->where('user_id', '=', $user_id)->first();
            
            if ($achievement === null) { //create new
                $achievement = Achievement::firstOrCreate([
                            "referenceable_type" => $input['referenceable_type'],
                            "referenceable_id"   => $input['referenceable_id'],
                             "user_id"           => $user_id,
                            "status"             => $this->calculateStatus($input),
                            "owner_id"           => auth()->user()->id,	
                ]); 
            } else { //update
                $achievement->update([
                            "referenceable_type" => $input['referenceable_type'],
                            "referenceable_id"   => $input['referenceable_id'],
                             "user_id"           => $user_id,
                            "status"             => $this->calculateStatus($input, $achievement->status),
                            "owner_id"           => auth()->user()->id,	
                ]); 
            }
            
            $achievement->save(); 
        
        }
        // axios call? 
        if (request()->wantsJson()){    
            return ['message' => $achievement->status];
        }
        return $achievement;
    }
    
    

    /* calculate proper status id */
    protected function calculateStatus($input, $status = '00')
    {
        if(\Gate::allows('achievement_create'))
        {
            $status[1] = $input['status'];
        }
        elseif (\Gate::allows('achievement_self_assessment'))
        {
            $status[0] = $input['status'];
        }
        //dd($status);
        return $status;
    }
    
    
    protected function validateRequest()
    {               
        
        return request()->validate([
            'referenceable_type'    => 'required',
            'referenceable_id'      => 'required',
            'user_id'               => 'sometimes',
            'status'                => 'required'
            ]);
    }
}
