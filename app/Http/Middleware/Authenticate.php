<?php

namespace App\Http\Middleware;

use Closure;
use Aacotroneo\Saml2\Saml2Auth;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\LogController;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards) {
        if (auth()->user() === null && env('APP_ENV') != 'local') {
            $saml2Auth = new Saml2Auth(Saml2Auth::loadOneLoginAuthFromIpdConfig('rlp'));
            // check if user is already authenticated at IDP
            if ($saml2Auth->isAuthenticated()) {
                $common_name = $saml2Auth->getSaml2User()->getAttribute('cn')[0];
                // authenticate user by common name
                \Illuminate\Support\Facades\Auth::login(\App\User::where('common_name', $common_name)->firstOrFail());
            } else {
                // only redirect to SSO login if request isn't a sharing-token link
                if ($request->has('sharing_token')) {
                    // set statistics for guest-authentication
                    LogController::set('guestLogin');
                    LogController::setStatistics();
                } else {
                    return $saml2Auth->login(URL::full()); // redirect to SSO login page
                }
            }
        }

        // authenticate as guest if no user is logged in (only on sharing-token links)
        Middleware::authenticate($request, $guards);

        return $next($request);
    }
}