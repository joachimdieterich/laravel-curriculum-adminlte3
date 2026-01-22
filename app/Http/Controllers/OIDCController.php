<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Jumbojett\OpenIDConnectClient;
use Illuminate\Support\Facades\Auth;

class OIDCController extends Controller
{
    /**
     * Handle OIDC authentication
     */
    public function handle(Request $request)
    {
        $oidc = new OpenIDConnectClient(
            env('OIDC_RLP_IDP_HOST'),
            env('OIDC_CLIENT_ID'),
            env('OIDC_CLIENT_SECRET')
        );
        
        $oidc->authenticate(); // authenticates user and saves tokens in instance

        if (isset($_SESSION['init_logout']) and $_SESSION['init_logout'] === true) {
            unset($_SESSION['init_logout']);

            // logout user locally
            Auth::guard()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // RP-initiated logout
            $oidc->signOut($oidc->getIdToken(), null); // calls 'exit;' internally to stop further execution
        }

        try {
            $common_name = $oidc->requestUserInfo('sub');
            // login user by common_name
            Auth::login(\App\User::select('id')->where('common_name', $common_name)->firstOrFail(), true);
            LogController::set('ssoLogin'); // set statistics for SSO-authentication
        } catch (\Throwable $th) {
            // if user not authenticated, login as guest user
            // if ($page_allows_guest) ...loginAsGuest
            // else return redirect($to_idp_login_page);
            throw $th; // for debugging
            LogController::set('guestLogin'); // set statistics for guest-authentication
            Auth::loginUsingId((env('GUEST_USER')), true);
        }
        
        LogController::setStatistics(); // set statistics for used browser and device type

        // since the user got redirected back after authentication, redirect to the originally requested URL
        if (isset($_REQUEST['code'])) {
            $redirect = $_SESSION['redirect_to'];
            unset($_SESSION['redirect_to']);
            return redirect($redirect);
        }
    }

    /**
     * Handle OIDC backchannel logout
     * @param  \Illuminate\Http\Request  $request contains logout_token
     * @return \Illuminate\Http\Response
     */
    public function backchannelLogout(Request $request): \Illuminate\Http\Response
    {
        $oidc = new OpenIDConnectClient(
            env('OIDC_RLP_IDP_HOST'),
            env('OIDC_CLIENT_ID'),
            env('OIDC_CLIENT_SECRET')
        );

        if (!$oidc->verifyLogoutToken()) return response('Could not verify logout token', 400);

        $common_name = $oidc->getVerifiedClaims('sub');

        $user = \App\User::select('id')->where('common_name', $common_name)->first();
        if ($user) {
            Auth::setUser($user);
            Auth::guard()->logout();
        }
            
        // if user cannot be found, still return success, or else it will retry sending the logout token
        return response('User logged out', 200);
    }
}