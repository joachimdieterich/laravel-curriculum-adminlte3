<?php

namespace App\Listeners;

use Aacotroneo\Saml2\Events\Saml2LoginEvent;
use App\Http\Controllers\LogController;
use App\Notifications\Welcome;
use App\Organization;
use App\OrganizationRoleUser;
use App\Role;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

        $sso_user = $event->getSaml2User();
        session(['sessionIndex' => $sso_user->getSessionIndex()]);
        session(['nameId' => $sso_user->getNameId()]);
        //dump($sso_user->getAttribute('cn'));
        $laravelUser = User::where('username', $sso_user->getAttribute('cn'))->first(); //find user by ID or attribute
        //if it does not exist create it and go on or show an error message
        if ($laravelUser) {
            //dump($laravelUser);
            Auth::login($laravelUser);
        }
        else //-- if sso_user does not exist. Create!
        {
            if (User::withTrashed()->where('common_name', $sso_user->getAttribute('cn'))->exists())
            {
                User::withTrashed()->where('common_name', $sso_user->getAttribute('cn'))->restore();
                $user = User::where('common_name', $sso_user->getAttribute('cn'))->get()->first();
                $user->update([
                    'email' => $sso_user->getAttribute('mail'),
                    'firstname' => $sso_user->getAttribute('givenname'),
                    'lastname' => $sso_user->getAttribute('sn'),
                ]);
            }
            else
            {
                dump(Str::uuid());
                dump(Hash::make(Str::uuid()));
                if ($user = User::create(
                    [
                        'username' => $sso_user->getAttribute('username'),
                        'common_name' => $sso_user->getAttribute('cn'),
                        'email' => $sso_user->getAttribute('mail'),
                        'firstname' => $sso_user->getAttribute('givenname'),
                        'lastname' => $sso_user->getAttribute('sn'),
                        'password' => Hash::make(Str::uuid()),
                    ])
                )
                {
                    $user->notify(new Welcome());
                }
            }

            // Enrol user to (creators) institution. Every user have to be enrolled to an institution!
            $org_id = Organization::where('common_name', $sso_user->getAttribute('rpidmprimaryorganisationdn'))->first()->id;
            switch ($sso_user->getAttribute('rpidmcategory'))
            {
                case 'Schooladmin':
                    $role_id = Role::where('title', 'Schooladmin')->first()->id;
                    break;
                case 'Lehrkraft':
                case 'LehrerInRP':
                    $role_id = Role::where('title', 'Teacher')->first()->id;
                    break;
                case 'SchülerIn':
                    $role_id = Role::where('title', 'Student')->first()->id;
                    break;
                case 'Sorgeberechtigte':
                    $role_id = Role::where('title', 'Parent')->first()->id;
                    break;
                case 'Sonstige':
                    $role_id = Role::where('title', 'Guest')->first()->id;// Guest
                    break;
                default:
                    $role_id = Role::where('title', 'Guest')->first()->id;// Guest
            }

            OrganizationRoleUser::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'organization_id' => $org_id,
                ],
                [
                    'role_id' => $role_id,
                ]
            );
            $user->current_organization_id = $org_id;
            $user->save();

            Auth::login($user); //login new user
        }

        // if users current_organization_id is not set -> get first organization as default
        $u = $u = \App\User::find(auth()->user()->id);
        if ($u->current_organization_id === null)
        {
            // if provisioning is correct set current_organization_id else abort
            $u->current_organization_id = (auth()->user()->organizations()->first() === null) ? abort(422) : auth()->user()->organizations()->first()->id;
            $u->save();
        }
        // if users current_period_id is not set -> if not enroled in group, current_period_id == null
        if ($u->current_period_id === null)
        {
            $u->current_period_id = optional(DB::table('periods')
                    ->select('periods.*')
                    ->join('groups', 'groups.period_id', '=', 'periods.id')
                    ->join('group_user', 'group_user.group_id', '=', 'groups.id')
                    ->where('group_user.user_id', $u->id)
                    ->where('groups.organization_id', $u->current_organization_id)
                    ->get()->first())->id;
            $u->save();
        }

        LogController::setStatistics();
        LogController::set('ssoLogin');
        LogController::set('activeOrg', auth()->user()->current_organization_id);
    }
}
