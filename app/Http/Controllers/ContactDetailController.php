<?php

namespace App\Http\Controllers;

use App\ContactDetail;
use App\User;
use App\Organization;
use Illuminate\Http\Request;

class ContactDetailController extends Controller
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
        return view('contactdetails.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('contactdetail_create'), 403);
        
        $input = $this->validateRequest();
        $contactdetail = ContactDetail::firstOrCreate([
            'email' => $input['email'],
            'phone' => $input['phone'],
            'mobile'=> $input['mobile'],
            'notes' => $input['notes'],  
            'owner_id' => auth()->user()->id,  
        ]);
         // axios call? 
        if (request()->wantsJson()){    
            return ['message' => $contactdetail->path()];
        }
        
        return redirect($contactdetail->path());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ContactDetail $contactDetail
     * @return \Illuminate\Http\Response
     */
    public function show(ContactDetail $contactdetail)
    {
        abort_unless(\Gate::allows('contactdetail_show'), 403);
        
        $user = User::find($contactdetail->owner_id);
        $organization = Organization::find(auth()->user()->current_organization_id);
        // axios call? 
        if (request()->wantsJson()){  
            return [
                'contactdetail' => $contactdetail
            ];
        }
        
        return view('contactdetails.show')
                ->with(compact('contactdetail'))
                ->with(compact('user'))
                ->with(compact('organization'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ContactDetail $contactDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(ContactDetail $contactdetail)
    {
        abort_unless(\Gate::allows('contactdetail_edit'), 403);
        
        return view('contactdetails.edit')
                ->with(compact('contactdetail'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ContactDetail $contactDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ContactDetail $contactdetail)
    {
        abort_unless(\Gate::allows('contactdetail_edit'), 403); 
        
        $contactdetail->update($this->validateRequest());
        
        return redirect($contactdetail->path());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ContactDetail $contactDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(ContactDetail $contactdetail)
    {
        //
    }
    
     protected function validateRequest(){
        return request()->validate([
            'email'  => 'sometimes|email',
            'phone'  => 'sometimes',
            'mobile' => 'sometimes',
            'notes'  => 'sometimes'
            ]);
    }
}
