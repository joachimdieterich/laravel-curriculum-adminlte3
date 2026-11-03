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
        $tokens = LmsUserToken::select('organization_id')->where('user_id', auth()->user()->id)->get();

        return Organization::whereIn('id', $tokens->pluck('organization_id'))
            ->get(['id', 'title', 'lms_url']);
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