<?php

namespace App\Services;

use App\Curriculum;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Jumbojett\OpenIDConnectClient;
use Throwable;
use URL;

class Authenticator
{
    /**
     * @throw HttpResponseException
     */
    public static function authenticate($request): void
    {
        $user_id = auth()->user()?->id;
        $path    = explode('/', $request->getRequestUri());

        if ($user_id === null || $user_id == config('app.guest_user_id')) {
            if (config('app.env') == 'local') {
                throw new HttpResponseException(redirect()->route('login'));
            }

            $allow_guest = $request->has('sharing_token')
                           || str_starts_with($path[1], 'navigator') // '/navigators' or '/navigatorViews' page
                           || $path[1] === 'eventSubscriptions'
                           || str_ends_with($request->getPathInfo(), 'startWithPw'); // videoconference-link;

            if (! $allow_guest) { // without token
                if (str_starts_with($request->getRequestUri(), '/curricula/')) { // '/curricula/{id}' page
                    // allow access if curriculum is accessible for guests
                    $allow_guest = Curriculum::select('type_id')->find($request->route('curriculum'))->type_id == 1;
                } elseif (
                    str_starts_with($request->getRequestUri(), '/terminalObjectives/') // '/terminalObjectives/{id}' page
                    || str_starts_with($request->getRequestUri(), '/enablingObjectives/') // '/enablingObjectives/{id}' page
                ) {
                    $table         = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $path[1]));
                    $curriculum_id = DB::table($table)->where('id', $path[2])->pluck('curriculum_id')->first();
                    $allow_guest   = Curriculum::select('type_id')->find($curriculum_id)->type_id == 1;
                }
            }

            // skip authentication if authenticated as guest and guest access is allowed
            if (! $allow_guest || $user_id != config('app.guest_user_id')) {
                $oidc = new OpenIDConnectClient(
                    config('app.oidc_host'),
                    config('app.oidc_client_id'),
                    config('app.oidc_client_secret')
                );

                // we need to use PHP's session-handler, because of the OIDC-library
                // if we use Laravel's session-handler, the value will not be accessible later,
                // because the session will have changed for some reason
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                // store current URL to redirect back after authentication-callback
                if (! isset($_SESSION['redirect_to'])) {
                    $_SESSION['redirect_to'] = URL::full();
                }

                // $oidc->setCodeChallengeMethod('S256'); // PKCE

                // if resource is accessible for guests, request silent authentication
                if ($allow_guest) {
                    $oidc->addAuthParam(['prompt' => 'none']);
                }

                // this will call the authorization endpoint and redirect to our OIDC-handling route
                $oidc->setRedirectURL(config('app.url') . '/oidc');

                try {
                    $oidc->authenticate();
                } catch (Throwable) {
                    abort(503, 'global.error.oidc');
                }
            }
        }
    }
}
