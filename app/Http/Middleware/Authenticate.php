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
        // only redirect to SSO login if request isn't a sharing-token link
        if (env('APP_ENV') != 'local' && auth()->user() === null) {
            if ($request->has('sharing_token')) {
                // set statistics for guest-authentication
                LogController::set('guestLogin');
                LogController::setStatistics();
            } else {
                // redirect to SSO login page
                $saml2Auth = new Saml2Auth(Saml2Auth::loadOneLoginAuthFromIpdConfig('rlp'));
                return $saml2Auth->login(URL::full());
            }
        }

        // authenticate as guest if no user is logged in (only on sharing-token links)
        Middleware::authenticate($request, $guards);

        return $next($request);
    }
}