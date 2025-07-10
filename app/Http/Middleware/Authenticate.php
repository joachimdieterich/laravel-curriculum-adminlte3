<?php

namespace App\Http\Middleware;

use Closure;
use Aacotroneo\Saml2\Saml2Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LogController;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards) {
        // dump(auth()->user());
        // dump(auth()->user);
        // dump(auth()->user === null);
        if ((auth()->user() === null or auth()->user()->id == env('GUEST_USER')) and env('APP_ENV') != 'local') {
            $saml2Auth = new Saml2Auth(Saml2Auth::loadOneLoginAuthFromIpdConfig('rlp'));
            // check if user is already authenticated at IDP
            if ($saml2Auth->isAuthenticated()) {
                $common_name = $saml2Auth->getSaml2User()->getAttribute('cn')[0];
                // authenticate user by common name
                Auth::login(\App\User::where('common_name', $common_name)->firstOrFail(), true);
            } else {
                // only redirect to SSO login if request isn't available to guests
                if (
                    $request->has('sharing_token')
                    or str_starts_with($request->getRequestUri(), '/navigator')
                    or str_ends_with($request->getPathInfo(), 'startWithPw') // videoconference-link
                ) {
                    // set statistics for guest-authentication
                    LogController::set('guestLogin');
                    LogController::setStatistics();
                    Auth::loginUsingId((env('GUEST_USER')), true);
                } else {
                    return $saml2Auth->login(URL::full()); // after successful login, redirect to the current URL
                }
            }
        }
        // needed to redirect to login-page in local environment
        Middleware::authenticate($request, $guards);

        return $next($request);
    }
}