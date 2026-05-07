<?php

namespace App\Http\Middleware;

use Closure;
use Jumbojett\OpenIDConnectClient;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards) {
        $user_id = auth()->user()?->id;

        if (($user_id === null or $user_id == config('app.guest_user_id')) and config('app.env') != 'local') {
            $allow_guest = $request->has('sharing_token')
                || str_starts_with($request->getRequestUri(), '/navigator')
                || str_starts_with($request->getRequestUri(), '/eventSubscriptions')
                || str_ends_with($request->getPathInfo(), 'startWithPw'); // videoconference-link;

            // if '/curricula/{id}' page (without token)
            if (!$allow_guest && str_starts_with($request->getRequestUri(), '/curricula/')) {
                // check if curriculum is accessible for guests
                // if not, force authentication
                $allow_guest = \App\Curriculum::select('type_id')->find($request->route('curriculum'))->type_id == 1;
            }

            // skip authentication if authenticated as guest and guest access is allowed
            if ($user_id != config('app.guest_user_id') or !$allow_guest) {
                $oidc = new OpenIDConnectClient(
                    config('app.oidc_host'),
                    config('app.oidc_client_id'),
                    config('app.oidc_client_secret')
                );
    
                // we need to use PHP's session-handler, because of the OIDC-library
                // if we use Laravel's session-handler, the value will not be accessible later,
                // because the session will have changed for some reason
                if (session_status() === PHP_SESSION_NONE) session_start();
                // store current URL to redirect back after authentication-callback
                if (!isset($_SESSION['redirect_to'])) $_SESSION['redirect_to'] = URL::full();

                // $oidc->setCodeChallengeMethod('S256'); // PKCE
                
                // if resource is accessible for guests, request silent authentication
                if ($allow_guest) $oidc->addAuthParam(['prompt' => 'none']);
    
                // this will call the authorization endpoint and redirect to our OIDC-handling route
                $oidc->setRedirectURL(config('app.url') . '/oidc');
                $oidc->authenticate();
            }
        }
        // needed to redirect to login-page in local environment
        Middleware::authenticate($request, $guards);

        return $next($request);
    }
}