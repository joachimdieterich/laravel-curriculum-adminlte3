<?php

namespace App\Http\Controllers;

use App\Absence;
use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort(403);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('absence_create'), 403);
        $new_absence = $this->validateRequest();

        foreach ((array) $new_absence['absent_user_ids'] as $user_id) {
            $absence = Absence::updateOrCreate(
                [
                    'referenceable_type' => $new_absence['referenceable_type'],
                    'referenceable_id'   => $new_absence['referenceable_id'],
                    'absent_user_id'    => $user_id,
                ],
                [
                    'reason'             => $new_absence['reason'],
                    'done'               => isset($new_absence['done']) ? $new_absence['done'] : 0,
                    'time'               => isset($new_absence['time']) ? $new_absence['time'] : 0,
                    'owner_id'           => auth()->user()->id,
                ]
            );
        }
        // axios call?
        if (request()->wantsJson()) {
            return ['message' => $absence];
        }
        abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Absence  $absence
     * @return \Illuminate\Http\Response
     */
    public function show(Absence $absence)
    {
        abort(403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Absence  $absence
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Absence $absence)
    {
        abort_unless((\Gate::allows('absence_edit') and $absence->isAccessible()), 403);

        if (request()->wantsJson()) {
            if ($absence->update($request->all())) {
                return ['done' => $absence->done];
            }
        }
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Absence  $absence
     * @return \Illuminate\Http\Response
     */
    public function destroy(Absence $absence)
    {
        abort_unless((\Gate::allows('absence_delete') and $absence->isAccessible()), 403);

        if (request()->wantsJson()) {
            return ['message' => $absence->delete()];
        }
        abort(404);
    }

    protected function validateRequest()
    {
        return request()->validate([
            'id'                 => 'sometimes',
            'reason'             => 'sometimes|required',
            'absent_user_ids'    => 'sometimes',
            'done'               => 'sometimes',
            'time'               => 'sometimes',
            'owner_id'           => 'sometimes',
            'referenceable_type' => 'sometimes',
            'referenceable_id'   => 'sometimes',
        ]);
    }
}
