<?php

namespace App\Http\Controllers;

use App\Plugins\Eventmanagement\EventmanagementPlugin;
use Illuminate\Http\Request;

class EventSubscriptionController extends Controller
{

    public function getEvents(Request $request)
    {
        $input = $this->validateRequest();
//        $subscriptions = RepositorySubscription::where('subscribable_type', $input['subscribable_type'])
//                ->where('subscribable_id', $input['subscribable_id'])
//                ->where('repository', $input['repository'])->get();
//        $result = collect([]);
//        foreach($subscriptions as $subscription)
//        {
//            $result->push($this->callPlugin($input['repository'], $subscription));
//        }

        $vm = new EventmanagementPlugin();
        $events = $vm->plugins[env('EVENTMANAGEMENTPLUGIN')]->lesePlrlpVeranstaltungen(['search'=> $input['search'], 'page' => $input['page']]);

        LogController::set(get_class($this).'@'.__FUNCTION__, $input['search'], (int) $events->lesePlrlpVeranstaltungen->GESAMT);

        if (request()->wantsJson()){
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
