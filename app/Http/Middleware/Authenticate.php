<?php

namespace App\Http\Middleware;

use Closure;
use Aacotroneo\Saml2\Saml2Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    // protected $saml2;

    // public function __construct(Saml2Auth $saml2)
    // {
    //     $this->saml2 = $saml2;
    // }

    public function handle($request, Closure $next, ...$guards) {
        Middleware::authenticate($request, $guards);

        // rough scetch of SSO login handling
        // dump($this->saml2->isAuthenticated());
        // check sso login
        // if (!$this->saml2->isAuthenticated()) {
        //     // check if link contains a sharing token
        //     if ($request->has('sharing_token')) { // INFO: Routes need to be changed to go through middleware
        //         return $next($request); // don't redirect to SSO login and continue as guest
        //     } else { // if not authenticated, redirect to SSO login
        //         return $this->saml2->login(URL::full());
                
        //         // loadOneLogin... always redirects to SSO login
        //         // $saml2Auth = new Saml2Auth(Saml2Auth::loadOneLoginAuthFromIpdConfig('rlp'));
        //         // return $saml2Auth->login(URL::full());
        //     }
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
