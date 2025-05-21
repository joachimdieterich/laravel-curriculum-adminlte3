<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards) {
        Middleware::authenticate($request, $guards);
        // dump(session('sessionIndex'));
        // dump(session('nameId'));
        // dump(session('cn'));

        // if (!is_null(session('cn'))) { // if SSO-session is given
        //     $user = \App\User::where('cn', session('cn'))->first();
        //     if (!is_null($user)) {
        //         Auth::login($user); // try to authenticate user
        //     }
        // }
        // else // if not, redirect to IDP-login page
        // {
        //     // code...
        // }

        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
