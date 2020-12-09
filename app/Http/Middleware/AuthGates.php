<?php

namespace App\Http\Middleware;

use App\Role;
use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;

class AuthGates
{
    public function handle($request, Closure $next)
    {

        $user = \Auth::user();

        if (/* !app()->runningInConsole() && */ $user) { // get permissions in console
            //$roles = Role::with('permissions')->get();
            $roles =  Cache::rememberForever('roles', function () {
                return Role::with('permissions')->get();
            });

            foreach ($roles as $role) {
                foreach ($role->permissions as $permissions) {
                    $permissionsArray[$permissions->title][] = $role->id;
                }
            }

            foreach ($permissionsArray as $title => $roles) {
                Gate::define($title, function (\App\User $user) use ($title, $roles) {
                    return in_array($user->role()->id, $roles) ? true : false; //only check current role
                    //return count(array_intersect($user->roles->pluck('id')->toArray(), $roles)) > 0; //check all users roles
                });
            }

            //set current organization and current period if not set
            if ($user->current_organization_id === NULL)
            {
                $user->current_organization_id = $user->organizations()->first()->id;
                $user->save();
            }

            if ($user->current_period_id === NULL)
            {
                $user->current_period_id = optional(DB::table('periods')
                        ->select('periods.*')
                        ->join('groups', 'groups.period_id', '=', 'periods.id')
                        ->join('group_user', 'group_user.group_id', '=', 'groups.id')
                        ->where('group_user.user_id',  $user->id)
                        ->where('groups.organization_id', $user->current_organization_id)
                        ->get()->first())->id;
                $user->save();
            }

        }

        return $next($request);
    }
}
