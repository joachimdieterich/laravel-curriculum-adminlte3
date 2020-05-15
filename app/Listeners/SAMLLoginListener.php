<?php

namespace App\Listeners;
use App\User;
use Aacotroneo\Saml2\Events\Saml2LoginEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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
            $userData = [
                'id' => $user->getUserId(),
                'attributes' => $user->getAttributes(),
                'assertion' => $user->getRawSamlAssertion()
            ];   
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
    }
}
