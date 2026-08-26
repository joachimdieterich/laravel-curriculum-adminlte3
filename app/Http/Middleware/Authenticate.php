<?php

namespace App\Http\Middleware;

use Closure;
use Jumbojett\OpenIDConnectClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards) {
        $user_id = auth()->user()?->id;
        $path = explode('/', $request->getRequestUri());

        if (($user_id === null or $user_id == config('app.guest_user_id')) and config('app.env') != 'local') {
            $allow_guest = $request->has('sharing_token')
                || $path[1] === 'navigator'
                || $path[1] === 'eventSubscriptions'
                || str_ends_with($request->getPathInfo(), 'startWithPw'); // videoconference-link;

            if (!$allow_guest) { // without token
                if (str_starts_with($request->getRequestUri(), '/curricula/')) { // '/curricula/{id}' page
                    // allow access if curriculum is accessible for guests
                    $allow_guest = \App\Curriculum::select('type_id')->find($request->route('curriculum'))->type_id == 1;
                } else if (
                    str_starts_with($request->getRequestUr(), '/terminalObjectives/') // '/terminalObjectives/{id}' page
                    || str_starts_with($request->getRequestUr(), '/enablingObjectives/') // '/enablingObjectives/{id}' page
                ) {
                    $table = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $path[1]));
                    $curriculum_id = DB::table($table)->where('id', $path[2])->pluck('curriculum_id')->first();
                    $allow_guest = \App\Curriculum::select('type_id')->find($curriculum_id)->type_id == 1;
                }
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

                try {
                    $oidc->authenticate();
                } catch (\Throwable) {
                    abort(503, 'global.error.oidc');
                }
            }
        }
        // needed to redirect to login-page in local environment
        Middleware::authenticate($request, $guards);

        return $next($request);
    }
}