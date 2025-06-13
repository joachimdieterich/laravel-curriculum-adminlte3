<?php

namespace App\Http\Middleware;

use Closure;
use Aacotroneo\Saml2\Saml2Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards) {
        // rough sketch of SSO login handling
        // INFO: Token-Routes need to be changed to go through this middleware
        // only redirect to SSO login if request isn't a sharing-token link
        // if (auth()->user() === null and !$request->has('sharing_token')) {
        //     // loadOneLogin... always redirects to SSO login
        //     $saml2Auth = new Saml2Auth(Saml2Auth::loadOneLoginAuthFromIpdConfig('rlp'));
        //     return $saml2Auth->login(URL::full());
        // }

        Middleware::authenticate($request, $guards); // authenticate as guest if no user is logged in

        return $next($request);
    }
}