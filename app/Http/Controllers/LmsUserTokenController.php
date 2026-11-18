<?php

namespace App\Http\Controllers;

use App\LmsUserToken;
use App\Organization;
use Illuminate\Http\Request;

class LmsUserTokenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // only need one valid token, since the moodle-endpoint returns courses from all user-connected instances
        $organization_id = LmsUserToken::select('organization_id')
            ->where('user_id', auth()->user()->id)
            ->first()?->organization_id;

        return Organization::select('id', 'lms_url')
            ->find($organization_id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $this->validateRequest();

        $token = LmsUserToken::updateOrCreate(
            [
                'organization_id' => $input['organization_id'],
                'user_id'         => auth()->user()->id,
            ],
            [
                'token' => $input['token'],
            ]);

        return $token;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\LmsUserToken  $lmsUserToken
     * @return \Illuminate\Http\Response
     */
    public function destroy(LmsUserToken $lmsUserToken)
    {
        //todo: delete if user is deleted
    }

    protected function validateRequest()
    {
        return request()->validate([
            'token'           => 'required|string',
            'organization_id' => 'required|exists:organizations,id',
        ]);
    }
}