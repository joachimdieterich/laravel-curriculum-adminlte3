<?php

namespace App\Http\Middleware;

use App\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AuthGates
{
    public function handle(Request $request, Closure $next)
    {
        $user = \Auth::user();

        if ($user) {
            // TTL is set to 15 minutes, as permissions are not expected to change frequently
            $rolePermissions = Cache::remember('role-permissions', now()->addMinutes(15), function() {
                $roles = Role::with('permissions')->get();
            
                $permissionsArray = [];
                foreach ($roles as $role) {
                    foreach ($role->permissions as $permissions) {
                        $permissionsArray[$permissions->title][] = $role->id;
                    }
                }
                return $permissionsArray;
            });

            // set current organization and current period if not set
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

            $current_role_id = ($user->role() !== null) ? $user->role()->id : abort(403, 'Fehlende Organisationszugehörigkeit');

            foreach ($rolePermissions as $permission => $roles) {
                Gate::define($permission, function() use ($current_role_id, $roles) {
                    return in_array($current_role_id, $roles) ? true : false;
                });
            }
        }

        return $next($request);
    }
}