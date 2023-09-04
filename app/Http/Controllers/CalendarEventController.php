<?php

namespace App\Http\Controllers;

use App\CalendarEvent;
use Illuminate\Http\Request;

class CalendarEventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $input = $this->validateRequest();

        $data = CalendarEvent::whereDate('start', '>=', $input['start'] ?? '2020-01-01')
            ->whereDate('end',   '<=', $input['end'] ?? '2024-01-01')
            ->get([
                'id',
                'title',
                'start',
                'startStr',
                'end',
                'endStr',
                'daysOfWeek',
                'endRecur'
                ]);
        return response()->json($data);
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
        $input = $this->validateRequest();

        $event = CalendarEvent::create([
            'title' => $input['title'],
            'start' => $input['start'],
            'startStr' => date($input['start']),
            'end' => $input['end'],
            'endStr' => date($input['end']),
            'owner_id' => auth()->user()->id,
        ]);

        return response()->json($event);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CalendarEvent  $calendarEvent
     * @return \Illuminate\Http\Response
     */
    public function show(CalendarEvent $calendarEvent)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CalendarEvent  $calendarEvent
     * @return \Illuminate\Http\Response
     */
    public function edit(CalendarEvent $calendarEvent)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CalendarEvent  $calendarEvent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CalendarEvent $calendarEvent)
    {
        $input = $this->validateRequest();

        $event = CalendarEvent::find($request->id)->update([
            'title' => $input['title'],
            'start' => $input['start'],
            'end' => $input['end'],
        ]);

        return response()->json($event);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CalendarEvent  $calendarEvent
     * @return \Illuminate\Http\Response
     */
    public function destroy(CalendarEvent $calendarEvent)
    {

        $event = CalendarEvent::find($calendarEvent->id)->delete();

        return response()->json($event);
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title' => 'sometimes|string',
            'start' => 'sometimes|string',
            'startStr' => 'sometimes|string',
            'end' => 'sometimes|string',
            'endStr' => 'sometimes|string',
        ]);
    }
}
