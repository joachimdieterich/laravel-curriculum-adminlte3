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

            session_start();
            // store current URL to redirect back after login
            if (!isset($_SESSION['redirect_to'])) $_SESSION['redirect_to'] = URL::full();
            // $oidc->setCodeChallengeMethod('S256'); // PKCE
            $oidc->authenticate();

            try {
                $common_name = $oidc->requestUserInfo('sub');
                // login user by common_name
                Auth::login(\App\User::select('id')->where('common_name', $common_name)->firstOrFail(), true);
            } catch (\Throwable $th) {
                // if user not authenticated, login as guest user
                // if ($page_allows_guest) ...loginAsGuest
                // else return redirect($to_idp_login_page);
                // Auth::loginUsingId((env('GUEST_USER')), true);
                throw $th; // for debugging
            }

            // since the user got redirected back after authentication, redirect to the originally requested URL
            if (isset($_REQUEST['code'])) {
                $redirect = $_SESSION['redirect_to'];
                unset($_SESSION['redirect_to']);
                return redirect($redirect);
            }
        }
        // needed to redirect to login-page in local environment
        Middleware::authenticate($request, $guards);

        return $next($request);
    }
}
