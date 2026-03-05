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

            // store current URL to redirect back after authentication-callback
            if (!session('redirect_to')) session(['redirect_to' => URL::full()]);
            // $oidc->setCodeChallengeMethod('S256'); // PKCE

            $allow_guest = $request->has('sharing_token')
                or str_starts_with($request->getRequestUri(), '/navigator')
                or str_starts_with($request->getRequestUri(), '/eventSubscriptions')
                or str_ends_with($request->getPathInfo(), 'startWithPw'); // videoconference-link;
            // if resource is accessible for guests, request silent authentication
            if ($allow_guest) $oidc->addAuthParam(['prompt' => 'none']);

            // this will call the authorization endpoint and redirect to our OIDC-handling route
            $oidc->setRedirectURL(env('APP_URL') . '/oidc');
            $oidc->authenticate();
        }
        // needed to redirect to login-page in local environment
        Middleware::authenticate($request, $guards);

        return $next($request);
    }
}