<?php

namespace App\Http\Controllers;

use App\KanbanSubscription;
use App\OrganizationRoleUser;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class ShareTokenController extends Controller
{
    public function create(Request $request)
    {
        $input = $this->validateRequest();
        $model_id = $input['model_id'];
        $name = $input['name'];
        $date = $input['date'];
        if (! empty($date)) {
            $date = Carbon::parse($date);
        }

        // Create random hash token
        do {
            $token_key = Str::uuid();
        } while (User::where('sharing_token', '=', $token_key)->first() instanceof User);

        // Check if token name is already taken and if so add a suffix
        if (User::withTrashed()->where('username', '=', $name)->first() instanceof User) {
            $suffix = 0;
            do {
                $suffix++;
                $name = $input['name'].'_'.$suffix;
            } while (User::withTrashed()->where('username', '=', $name)->first() instanceof User);
        }

        $user = new User();
        $user->username = $name;
        $user->firstname = $user->lastname = '';
        $user->email = $token_key.'@curriculumonline.de';
        $user->password = '-';
        $user->sharing_token = $token_key;
        $user->current_organization_id = Auth::user()->current_organization_id;
        $user->save();

        $subscription = new KanbanSubscription();
        $subscription->kanban_id = $model_id;
        $subscription->owner_id = Auth::id();
        $subscription->subscribable_type = "App\User";
        $subscription->subscribable_id = $user->id;
        $subscription->due_date = $date;
        $subscription->save();

        OrganizationRoleUser::firstOrCreate(
            [
                'user_id' => $user->id,
                'organization_id' => Auth::user()->current_organization_id,
            ],
            [
                'role_id' => 9, // TOKEN
            ]
        );

        return response()->json(['url' => '/kanban/share/'.$token_key]);
    }

    public function auth($token)
    {
        $user = User::where('sharing_token', $token)->first();
        Auth::login($user, true);

        $subscription = $user->kanbanSubscription->first();
        if ($subscription->due_date) {
            $now = Carbon::now();
            $due_date = Carbon::parse($subscription->due_date);
            if ($due_date < $now) {
                abort(410, 'Dieser Link ist nicht mehr gültig');
            }
        }

        // Get subscription
        $kanban = $user->kanbans->first();

        return redirect('/kanbans/'.$kanban->id);
    }

    protected function validateRequest()
    {
        return request()->validate([
            'name' => 'string',
            'date' => 'nullable|date',
            'model_id' => 'integer',
            'editable' => 'sometimes',
        ]);
    }
}
