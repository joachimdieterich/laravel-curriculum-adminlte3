<?php

namespace App\Http\Middleware;

use App\Role;
use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AuthGates
{
    public function handle($request, Closure $next)
    {
        $user = \Auth::user();

        if (/* !app()->runningInConsole() && */ $user) { // get permissions in console
            $roles = Cache::rememberForever('roles', function () {
                return Role::with('permissions')->get();
            });

            foreach ($roles as $role) {
                foreach ($role->permissions as $permissions) {
                    $permissionsArray[$permissions->title][] = $role->id;
                }
            }

            $current_role_id = ($user->role() !== null) ? $user->role()->id : abort(403, 'Fehlende Organisationszugehörigkeit');
            //$current_role_id =  $user->role()->id;
            foreach ($permissionsArray as $title => $roles) {
                Gate::define($title, function () use ($current_role_id, $roles) {
                    return in_array($current_role_id, $roles) ? true : false; //only check current role
                });
            }

            //set current organization and current period if not set
            if ($user->current_organization_id === null) {
                $user->current_organization_id = $user->organizations()->first()->id;
                $user->save();
            }

            if ($user->current_period_id === null) {
                $user->current_period_id = optional(DB::table('periods')
                        ->select('periods.*')
                        ->join('groups', 'groups.period_id', '=', 'periods.id')
                        ->join('group_user', 'group_user.group_id', '=', 'groups.id')
                        ->where('group_user.user_id', $user->id)
                        ->where('groups.organization_id', $user->current_organization_id)
                        ->get()->first())->id;
                $user->save();
            }
        }

        return $next($request);
    }
}
