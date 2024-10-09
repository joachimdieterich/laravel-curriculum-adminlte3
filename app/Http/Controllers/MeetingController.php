<?php

namespace App\Http\Controllers;

use App\Meeting;
use App\MeetingDate;
use App\Organization;
use App\Plugins\Eventmanagement\EventmanagementPlugin;
use Illuminate\Http\Request;
use SimpleXMLElement;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class MeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_unless(\Gate::allows('meeting_access'), 403);
        return view('meetings.index');
    }

    public function userMeetings($withOwned = true)
    {
        $userCanSee = collect();
        $userCanSee->merge(auth()->user()->meetings);

        if (auth()->user()->sharing_token !== null)  //tokenuser? only return subscriptions
        {
            return $userCanSee;
        }

        foreach (auth()->user()->groups as $group) {
            $userCanSee = $userCanSee->merge($group->meetings);
        }
        $organization = Organization::find(auth()->user()->current_organization_id)->meetings;
        $userCanSee = $userCanSee->merge($organization);

        if ($withOwned)
        {
            $owned = Meeting::where('owner_id', auth()->user()->id)->get();
            $userCanSee = $userCanSee->merge($owned);
        }

        return $userCanSee->unique();
    }
    public function list(Request $request)
    {
        abort_unless(\Gate::allows('meeting_access'), 403);
        switch ($request->filter)
        {
            case 'owner':            $kanbans = Meeting::where('owner_id', auth()->user()->id)->get();
                break;
            case 'shared_with_me':   $kanbans = $this->userMeetings(false);
                break;
            case 'shared_by_me':     $kanbans = Meeting::where('owner_id', auth()->user()->id)->whereHas('subscriptions')->get();
                break;
            case 'all':
            default:                $kanbans = $this->userMeetings();
                break;
        }

        return empty($kanbans) ? '' : DataTables::of($kanbans)
            ->setRowId('id')
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('meetings.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //abort_unless(\Gate::allows('logbook_create'), 403);

        $input = $this->validateRequest();

        $meeting = Meeting::Create([
            'uid' => $input['uid'],
            'access_token' => $input['access_token'] ?? '',
            'title' => $input['title'],
            'subtitle' => $input['subtitle'],
            'description' => $input['description'],
            'begin' => $input['begin'],
            'end' => $input['end'],
            'status' => $input['status'],
            'category' => $input['category'],
            'target_group' => $input['target_group'],
            'url' => $input['url'],
            'provider' => $input['provider'],
            'color' => $input['color'],
            'medium_id' => $input['medium_id'],

            'owner_id' => auth()->user()->id,
        ]);

        if (request()->wantsJson()) {
            return $meeting;
        }
        //return view('meetings.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Meeting  $meeting
     * @return \Illuminate\Http\Response
     */
    public function show(Meeting $meeting)
    {
        return view('meetings.show')
            ->with(compact('meeting'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Meeting  $meeting
     * @return \Illuminate\Http\Response
     */
    public function edit(Meeting $meeting)
    {
        return view('meetings.edit')
            ->with(compact('meeting'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Meeting  $meeting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Meeting $meeting)
    {
        $input = $this->validateRequest();

        $meeting->update([
            'uid' => $input['uid'] ?? $meeting->uid,
            'access_token' => $input['access_token'] ?? $meeting->access_token,
            'title' => $input['title'] ?? $meeting->title,
            'subtitle' => $input['subtitle'] ?? $meeting->subtitle,
            'description' => $input['description'] ?? $meeting->begin,
            'begin' => $input['begin'] ?? $meeting->begin,
            'end' => $input['end'] ?? $meeting->end,
            'status' => $input['status'] ?? $meeting->status,
            'category' => $input['category'] ?? $meeting->target_group,
            'target_group' => $input['target_group'] ?? $meeting->target_group,
            'url' => $input['url']?? $meeting->url,
            'provider' => $input['provider'] ?? $meeting->provider,
            'color' => $input['color'] ?? $meeting->color,
            'medium_id' => $new_meeting['medium_id'] ?? $meeting->medium_id,

            'owner_id' => auth()->user()->id,
        ]);

        return $meeting;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Meeting  $meeting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Meeting $meeting)
    {
        $meeting->dates()->delete();
        $return = $meeting->delete();
        if (request()->wantsJson()) {
            return ['message' => $return];
        }
    }

    public function getImportDataByUid(Request $request){
        $new_meeting = $this->validateRequest();
        $vm = new EventmanagementPlugin();
        $events = $vm->plugins[env('EVENTMANAGEMENTPLUGIN')]->lesePlrlpVeranstaltungen(['search'=> $new_meeting['uid']]);
        //dump($events);
        $event = $events->lesePlrlpVeranstaltungen->data->key_0;

        $meeting = Meeting::Create(
            [
                'uid' => $event->ARTIKEL_NR->__toString() ?: $new_meeting['uid'],
                'access_token' => '',
                'title' => $event->ARTIKEL->__toString(),
                'subtitle' => $event->BEZ_1_2->__toString() ?? null,
                'description' => nl2br($event->BEMERKUNG),
                'begin' => $event->B_DAT->__toString(),
                'end' => $event->E_DAT->__toString(),
                'status' => $event->PLAN_STAT->__toString(),
                'category' => $event->KATEGORIE->__toString(),
                'target_group' => json_decode($event->ZIELGRUPPE->__toString()),
                'url' => $event->LINK_DETAIL->__toString(),
                'provider' => $event->ANBIETER->__toString(),

                'owner_id' => auth()->user()->id,
            ]
        );

        foreach ( (array) $event->termine as $index => $termin) {
            MeetingDate::Create([
                'meeting_id' => $meeting->id,
                'uid' => $termin->ARTIKEL_NR ?: $new_meeting['uid'],
                'access_token' => '',
                'title' => Carbon::createFromFormat('Y-m-d H:i:s', $termin->DATUM.' '.$termin->BEGINN.':00')->format('d.m.y').' '. $termin->ARTIKEL,
                'address' => $termin->VO_ADRESSE,
                'begin' => Carbon::createFromFormat('Y-m-d H:i:s', $termin->DATUM.' '.$termin->BEGINN.':00'),
                'end' => Carbon::createFromFormat('Y-m-d H:i:s', $termin->DATUM.' '.$termin->ENDE.':00'),
                'type' => $termin->VO_ORT,

                'owner_id' => auth()->user()->id,
            ]);
        }

        if (request()->wantsJson()) {
            return $meeting;
        }
    }


    protected function validateRequest()
    {
        return request()->validate([
            'uid' => 'sometimes|required',
            'access_token' => 'sometimes',
            'title' => 'sometimes',
            'subtitle' => 'sometimes',
            'description' => 'sometimes',
            'info' => 'sometimes',
            'speakers' => 'sometimes',
            'begin' => 'sometimes',
            'end' => 'sometimes',
            'status' => 'sometimes',
            'category' => 'sometimes',
            'target_group' => 'sometimes',
            'url' => 'sometimes',
            'provider' => 'sometimes',
            'medium_id' => 'sometimes',
            'owner_id' => 'sometimes',
            'livestream' => 'sometimes',
            'color' => 'sometimes',
        ]);
    }
}
