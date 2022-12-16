<?php

namespace App\Http\Controllers;

use App\MeetingDate;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class MeetingDateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $input = $this->validateRequest();

        $dates = MeetingDate::where([
            'meeting_id' => $input['meeting_id'],
        ]);

        if (request()->wantsJson()) {
            return ['dates' => $dates->with('agendas')->get()];
        }
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\MeetingDate  $meetingDate
     * @return \Illuminate\Http\Response
     */
    public function show(MeetingDate $meetingDate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MeetingDate  $meetingDate
     * @return \Illuminate\Http\Response
     */
    public function edit(MeetingDate $meetingDate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MeetingDate  $meetingDate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MeetingDate $meetingDate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MeetingDate  $meetingDate
     * @return \Illuminate\Http\Response
     */
    public function destroy(MeetingDate $meetingDate)
    {
        //
    }

    protected function validateRequest()
    {
        return request()->validate([
            'meeting_id'        => 'sometimes',
        ]);
    }
}
