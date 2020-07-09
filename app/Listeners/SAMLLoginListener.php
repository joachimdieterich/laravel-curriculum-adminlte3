<?php

namespace App\Listeners;
use App\User;
use Aacotroneo\Saml2\Events\Saml2LoginEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class SAMLLoginListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Saml2LoginEvent  $event
     * @return void
     */
    public function handle(Saml2LoginEvent $event)
    {
        $messageId = $event->getSaml2Auth()->getLastMessageId();
        // Add your own code preventing reuse of a $messageId to stop replay attacks

        $user = $event->getSaml2User();
        session(['sessionIndex' => $user->getSessionIndex()]);
        session(['nameId' => $user->getNameId()]);

        $laravelUser = User::where('username', $user->getUserId())->get();//find user by ID or attribute
         //if it does not exist create it and go on or show an error message        
        Auth::login($laravelUser->first());

        // if users current_organization_id is not set -> get first organization as default
        if (auth()->user()->current_organization_id === NULL)
        {
            $u = \App\User::find(auth()->user()->id);
            $u->current_organization_id = auth()->user()->organizations()->first()->id;
            $u->save();
        }       
        // if users current_period_id is not set -> if not enroled in group current_period_id == null
        if (auth()->user()->current_period_id === NULL)
        {
            $u = \App\User::find(auth()->user()->id);
            $u->current_period_id = optional(DB::table('periods')
                    ->select('periods.*')
                    ->join('groups', 'groups.period_id', '=', 'periods.id')
                    ->join('group_user', 'group_user.group_id', '=', 'groups.id') 
                    ->where('group_user.user_id',  $this->id)
                    ->where('groups.organization_id', $u->current_organization_id)
                    ->get()->first())->id;
            $u->save();
        }       
    }
}
