<?php

namespace App\Http\Controllers;

use App\Medium;
use File;
use Illuminate\Http\Request;
use Laravolt\Avatar\Avatar;
use Yajra\DataTables\DataTables;

class MediumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_unless(\Gate::allows('medium_access'), 403);
        
        return view('media.index');
    }
    
    public function list()
    {
        abort_unless(\Gate::allows('medium_access'), 403);
        $media = (auth()->user()->role()->id == 1) ? Medium::all() : auth()->user()->media()->get();
          
        $delete_gate = \Gate::allows('medium_delete');
        
        return DataTables::of($media)
            ->addColumn('action', function ($media) use ($delete_gate){
                 $actions  = '';
                  
                    if ($delete_gate){
                        $actions .= '<button type="button" '
                                . 'class="btn text-danger" '
                                . 'onclick="destroyDataTableEntry(\'media\','.$media->id.')">'
                                . '<i class="fa fa-trash"></i></button>';
                    }
              
                return $actions;
            })
           
            ->addColumn('check', '')
            ->setRowId('id')
            ->setRowAttr([
                'color' => 'primary',
            ])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return Medium::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Medium  $medium
     * @return \Illuminate\Http\Response
     */
    public function show(Medium $medium)
    {
        /* id link */
        if (($medium->mime_type != 'url') ){
            $path = storage_path('app'.$medium->path.$medium->medium_name);
            //dd($path);
            if (!file_exists($path)) {
                abort(404);
            } 
        } 
          
        /*
         * Medium is public (sharing_level_id == 1) or user is owner
         */
        if (($medium->public == true) OR ($medium->owner_id == auth()->user()->id))
        {
            return ($medium->mime_type != 'url') ? response()->file($path) : redirect($medium->path); //return file or url
        }
        
        /* checkIfUserHasSubscription and visibility*/
        if ($medium->subscriptions())
        {
            foreach ($medium->subscriptions AS $subscription)
            {
                if ($this->checkIfUserHasSubscription($subscription))
                {
                    return ($medium->mime_type != 'url') ? response()->file($path) : redirect($medium->path); //return file or url
                }
            }       
        } 
        /* end checkIfUserHasSubscription and visibility */
        
        /* user has permission to access this file ! */
        abort(403);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Medium  $medium
     * @return \Illuminate\Http\Response
     */
    public function edit(Medium $medium)
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Medium  $medium
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Medium $medium)
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Medium  $medium
     * @return \Illuminate\Http\Response
     */
    public function destroy(Medium $medium)
    {
        //
    }
    
    public function massDestroy(Medium $medium)
    {
        abort(404);
    }
    
    public function getMediumByEventPath($path)
    {
        $m = new Medium();
        return Medium::where('path', $m->convertFilemanagerEventPathToMediumPath($path))
                        ->where('medium_name', basename($path))
                        ->get()->first();
    }
    
    public function checkIfUserHasSubscription($subscription)
    {
        
        switch ($subscription->subscribable_type) {
            case "App\Organization": 
                if (in_array($subscription->subscribable_id, auth()->user()->organizations()->pluck('organization_id')->toArray()) 
                    AND ($subscription->visibility == 1))
                {
                    return true;
                }

                break;
            case "App\Group":   
                if (in_array($subscription->subscribable_id, auth()->user()->groups()->pluck('groups.id')->toArray())
                    AND ($subscription->visibility == 1))
                {
                    return true;
                }
                break;
            case "App\User":     
                
                if ($subscription->subscribable_id ==  auth()->user()->id 
                     AND ($subscription->visibility == 1))
                {
                    return true;
                }
                break;

            default: return false;
                break;
        }
    }
}
