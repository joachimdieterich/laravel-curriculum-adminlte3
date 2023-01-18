<?php

namespace App\Http\Controllers;

use App\Plugins\Eventmanagement\EventmanagementPlugin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventSubscriptionController extends Controller
{
    public function embed(Request $request)
    {
        if (Auth::user() == null && env('GUEST_USER') !== null) {       //if no user is authenticated authenticate guest
            LogController::set('guestLogin');
            LogController::setStatistics();
            Auth::loginUsingId((env('GUEST_USER')), true);
        }

        return view('embed.events.index');
    }

    public function getEvents(Request $request)
    {
        $input = $this->validateRequest();


        $vm = new EventmanagementPlugin();
        $events = $vm->plugins[env('EVENTMANAGEMENTPLUGIN')]->lesePlrlpVeranstaltungen(['search'=> $input['search'], 'page' => $input['page']]);

        LogController::set(get_class($this).'@'.__FUNCTION__, $input['search'], (int) $events->lesePlrlpVeranstaltungen->GESAMT);

        if (request()->wantsJson()) {
            return ['message' => $events];
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'value'             => 'sometimes',
            'subscribable_type' => 'sometimes|required',
            'subscribable_id'   => 'sometimes|required',
            'search'            => 'sometimes',
            'page'              => 'sometimes',
            'plugin'            => 'required',
        ]);
    }
}
