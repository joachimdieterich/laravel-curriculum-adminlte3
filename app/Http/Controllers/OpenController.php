<?php

namespace App\Http\Controllers;

use App\Content;
use App\User;
use App\Organization;
use App\OrganizationRoleUser;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Notifications\Welcome;
use Illuminate\Support\Facades\Auth;
use App\Role;

class OpenController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function features()
    {
        return view('features');
    }

    public function impressum()
    {
        $impressum = Content::where('title', '=', 'Impressum')->first();

        return view('impressum', compact('impressum'));
    }

    public function terms()
    {
        $terms = Content::where('title', '=', 'Terms')->first();

        return view('terms', compact('terms'));
    }

    public function wip()
    {
        abort(403);
        //test sso call for new users

        $sso_user = new \stdClass();
        $sso_user->cn = '1234';
        $sso_user->mail = '12345@curriculumonline.de';
        $sso_user->username = 'exists_not';
        $sso_user->givenname = '1234_givenname';
        $sso_user->sn = '1234_lastname';
        $sso_user->rpidmprimaryorganisationdn = 'DE-0000';
        $sso_user->rpidmcategory = 'SchülerIn';


        $laravelUser = User::where('username', $sso_user->username)->first(); //find user by ID or attribute
        //if it does not exist create it and go on or show an error message

        if ($laravelUser) {
            dump($laravelUser);
            Auth::login($laravelUser);
        } else //-- if user does not exist. Get
        {
            if (User::withTrashed()->where('common_name', $sso_user->cn)->exists()) {
                User::withTrashed()->where('common_name', $sso_user->cn)->restore();
                $user = User::where('common_name', $sso_user->cn)->get()->first();
                $user->update([
                    'email' => $sso_user->mail,
                    'firstname' => $sso_user->givenname,
                    'lastname' => $sso_user->sn,
                ]);
            } else {
                //end tempfix
                if ($user = User::create(
                    [
                        'username' => $sso_user->username,
                        'common_name' => $sso_user->cn,
                        'email' => $sso_user->mail,
                        'firstname' => $sso_user->givenname,
                        'lastname' => $sso_user->sn,
                        'password' => Hash::make(Str::uuid()),
                    ])
                ) {
                    $user->notify(new Welcome());
                }
            }
            // Enrol user to (creators) institution. Every user have to be enrolled to an institution!


            $org_id = Organization::where('common_name', $sso_user->rpidmprimaryorganisationdn)->first()->id;
            switch ($sso_user->rpidmcategory) {
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
    }
}
