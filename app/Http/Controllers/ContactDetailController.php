<?php

namespace App\Http\Controllers;

use App\ContactDetail;
use App\Organization;
use App\User;
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('contactdetail_create'), 403);

        $input = $this->validateRequest();
        $contactDetail = ContactDetail::firstOrCreate([
            'email' => $input['email'],
            'phone' => $input['phone'],
            'mobile'=> $input['mobile'],
            'notes' => $input['notes'],
            'owner_id' => auth()->user()->id,
        ]);

        if (request()->wantsJson()) {
            return $contactDetail;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ContactDetail  $contactDetail
     * @return \Illuminate\Http\Response
     */
    public function show(ContactDetail $contactDetail)
    {
        abort(403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ContactDetail  $contactDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ContactDetail $contactDetail)
    {
        abort_unless(\Gate::allows('contactdetail_edit'), 403);
        abort_unless(($contactDetail->owner_id == auth()->user()->id), 403);

        $contactDetail->update($this->validateRequest());

        if (request()->wantsJson()) {
            return $contactDetail;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ContactDetail  $contactDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(ContactDetail $contactDetail)
    {
        abort_unless(\Gate::allows('contactdetail_delete'), 403);
        abort_unless(($contactDetail->owner_id == auth()->user()->id), 403);

        return $contactDetail->delete();
    }

    protected function validateRequest()
    {
        return request()->validate([
            'email'  => 'sometimes|email',
            'phone'  => 'sometimes',
            'mobile' => 'sometimes',
            'notes'  => 'sometimes',
        ]);
    }
}
