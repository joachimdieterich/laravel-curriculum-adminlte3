<?php

namespace App\Http\Middleware;

use Closure;
use Jumbojett\OpenIDConnectClient;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LogController;
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

            // store current URL to redirect back after login
            if (!isset($_SESSION['redirect_to'])) $_SESSION['redirect_to'] = URL::full();
            // $oidc->setCodeChallengeMethod('S256'); // PKCE
            $oidc->authenticate();

            try {
                $userinfo = $oidc->requestUserInfo();
                // dd($userinfo);
                // login user by common_name
                // Auth::login(\App\User::select('id')->where('common_name', $userinfo->common_name)->firstOrFail(), true);
            } catch (\Throwable $th) {
                // if user not authenticated, login as guest user
                // Auth::loginUsingId((env('GUEST_USER')), true);
                throw $th; // for debugging
            }

            // since the user got redirected back after authentication, redirect to the originally requested URL
            // if (isset($_REQUEST['code'])) {
            //     $redirect = $_SESSION['redirect_to'];
            //     unset($_SESSION['redirect_to']);
            //     return redirect($redirect);
            // }

            /*** old SAML2-Auth ***/
            // check if user is already authenticated at IDP
            // if ($saml2Auth->isAuthenticated()) {
            //     $common_name = $saml2Auth->getSaml2User()->getAttribute('cn')[0];
            //     // authenticate user by common name
            //     Auth::login(\App\User::where('common_name', $common_name)->firstOrFail(), true);
            // } else {
            //     // only redirect to SSO login if request isn't available to guests
            //     if (
            //         !$request->has('forceSSO') and // TODO: temporary solution | remove after implementing OIDC
            //         (
            //             $request->has('sharing_token')
            //             or str_starts_with($request->getRequestUri(), '/navigator')
            //             or str_starts_with($request->getRequestUri(), '/eventSubscriptions')
            //             or str_ends_with($request->getPathInfo(), 'startWithPw') // videoconference-link
            //         )
            //     ) {
            //         // set statistics for guest-authentication
            //         LogController::set('guestLogin');
            //         LogController::setStatistics();
            //         Auth::loginUsingId((env('GUEST_USER')), true);
            //     } else {
            //         return $saml2Auth->login(URL::full()); // after successful login, redirect to the current URL
            //     }
            // }
        }
        // needed to redirect to login-page in local environment
        Middleware::authenticate($request, $guards);

        return $next($request);
    }
}
