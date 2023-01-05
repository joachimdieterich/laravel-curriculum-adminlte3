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

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('meeting_create'), 403);
        $new_meeting = $this->validateRequest();

        $meetingDate = MeetingDate::create([
            'uid'           => $new_meeting['uid'],
            'meeting_id'    => $new_meeting['meeting_id'],
            'access_token'  => $new_meeting['access_token'],
            'title'         => $new_meeting['title'],
            'description'   => $new_meeting['description'],
            'address'       => $new_meeting['address'],
            'begin'         => $new_meeting['begin'],
            'end'           => $new_meeting['end'],
            'type'          => $new_meeting['type'],

            'owner_id' => auth()->user()->id,
        ]);

        if (request()->wantsJson()) {
            return ['meetingDate' => $meetingDate];
        }
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
        $meetingDate->delete();
    }

    protected function validateRequest()
    {
        return request()->validate([
            'id' => 'sometimes',
            'uid' => 'sometimes',
            'meeting_id' => 'sometimes',
            'access_token' => 'sometimes',
            'title' => 'sometimes|required',
            'description' => 'sometimes',
            'address' => 'sometimes',
            'begin' => 'sometimes',
            'end' => 'sometimes',
            'owner_id' => 'sometimes',
            'type' => 'sometimes',
        ]);
    }
}
