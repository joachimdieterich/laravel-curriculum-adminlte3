<?php

namespace App\Http\Controllers;

use App\KanbanSubscription;
use App\VideoconferenceSubscription;
use App\CurriculumSubscription;
use App\MapSubscription;
use App\User;
use Carbon\Carbon;
use App\Helpers\QRCodeHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class ShareTokenController extends Controller
{
    public function create(Request $request)
    {
        $input = $this->validateRequest();
        $date = $input['date'];
        if (!empty($date)) {
            $date = Carbon::parse($date);
        }

        $user = User::find(env('GUEST_USER'));

        $subscriptionClass = null;
        $field_model_id = null;
        $model_url = null;

        switch ($input['model_url'])
        {
            case 'videoconference':
                $subscriptionClass = new VideoconferenceSubscription();
                $field_model_id = 'videoconference_id';
                $model_url = 'videoconferences';
                break;
            case 'kanban':
                $subscriptionClass = new KanbanSubscription();
                $field_model_id = 'kanban_id';
                $model_url = 'kanbans';
                break;
            case 'curriculum':
                $subscriptionClass = new CurriculumSubscription();
                $field_model_id = 'curriculum_id';
                $model_url = 'curricula';
                break;
            case 'map':
                $subscriptionClass = new MapSubscription();
                $field_model_id = 'map_id';
                $model_url = 'maps';
                break;
            default:
                abort(422, "Model doesn't accept sharing-tokens");
        }

        // Create random hash token
        $token = Str::uuid();

        $subscribe =  $subscriptionClass->create([
            $field_model_id => $input['model_id'],
            'subscribable_type' => "App\User",
            'subscribable_id' => $user->id,
            'sharing_token' => $token,
            'due_date' => $date,
            'title' => $input['title'] ?? false,
            'editable' => $input['editable'] ?? false,
            'owner_id' => auth()->user()->id,
        ]);

        return [
            "token" => $subscribe,
            "qr"    => (new QRCodeHelper())
                ->generateQRCodeByString(
                    env("APP_URL") . "/" . $model_url . "/" . $input['model_id'] . "/token?sharing_token=" . $subscribe->sharing_token
                ),
        ];
    }

    public function auth($token)
    {
        //only used by old kanban urls
        if (Auth::user() == null) {       //if no user is authenticated authenticate guest
            LogController::set('guestLogin');
            LogController::setStatistics();
            Auth::loginUsingId((env('GUEST_USER')), true);
        }

        $subscription = KanbanSubscription::where('sharing_token',$token)->get()->first();
        if ($subscription->due_date) {
            $now = Carbon::now();
            $due_date = Carbon::parse($subscription->due_date);
            if ($due_date < $now) {
                abort(410, 'Dieser Link ist nicht mehr gültig');
            }
        }

        return redirect('/kanbans/'.$subscription->kanban_id.'/token?sharing_token='.$token);
    }
    protected function validateRequest()
    {
        return request()->validate([
            'title' => 'string',
            'date' => 'nullable|date',
            'model_id' => 'integer',
            'editable' => 'sometimes',
            'model_url' => 'sometimes|string',
        ]);
    }
}
