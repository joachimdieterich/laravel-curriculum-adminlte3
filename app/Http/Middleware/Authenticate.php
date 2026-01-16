<?php

namespace App\Http\Middleware;

use Closure;
use Jumbojett\OpenIDConnectClient;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards) {
        $user = auth()->user();

        if (($user === null or $user->id == env('GUEST_USER')) and env('APP_ENV') != 'local') {
            $oidc = new OpenIDConnectClient(
                env('OIDC_RLP_IDP_HOST'),
                env('OIDC_CLIENT_ID'),
                env('OIDC_CLIENT_SECRET')
            );

            session_start();
            // store current URL to redirect back after authentication-callback
            if (!isset($_SESSION['redirect_to'])) $_SESSION['redirect_to'] = URL::full();
            // $oidc->setCodeChallengeMethod('S256'); // PKCE

            // this will call the authorization endpoint and redirect to our OIDC-handling route
            $oidc->setRedirectURL(env('APP_URL') . '/oidc');
            $oidc->authenticate();
        }
        // needed to redirect to login-page in local environment
        Middleware::authenticate($request, $guards);

        return $next($request);
    }
}