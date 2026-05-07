<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\VideoconferenceController;
use App\Videoconference;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class VideoconferenceApiController
{
    /**
     * Store a newly created resource in storage.
     *
     * @return Videoconference|void
     */
    public function store()
    {
        return new VideoconferenceController()->store();
    }

    /**
     * Build and return moderator and attendee links of videoconference
     *
     * @param Request $request
     * @return Collection
     */
    public function getLinks(Request $request): Collection
    {
        abort_unless(Gate::allows('videoconference_access') and auth()->user()->id !== config('app.guest_user_id'), 403, 'missing rights');

        $request->validate([
            'meetingID' => 'required|uuid|exists:videoconferences,meetingID',
        ]);

        /** @var Videoconference $videoconference */
        $videoconference = Videoconference::where('meetingID', $request->meetingID)->get()->first();

        return collect([
            'moderatorLink' => url()->query("/videoconferences/{$videoconference->id}/startWithPw", ['moderatorPW' => $videoconference->moderatorPW]),
            'attendeeLink'  => url()->query("/videoconferences/{$videoconference->id}/startWithPw", ['attendeePw' => $videoconference->attendeePW]),
        ]);
    }
}