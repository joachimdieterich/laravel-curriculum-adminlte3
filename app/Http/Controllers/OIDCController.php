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
    public function handle(Request $request): void
    {
        $oidc = new OpenIDConnectClient(
            env('OIDC_RLP_IDP_HOST'),
            env('OIDC_CLIENT_ID'),
            env('OIDC_CLIENT_SECRET')
        );
        
        $oidc->authenticate(); // authenticates user and saves tokens in instance
        session_start();

        if (isset($_SESSION['innit_logout']) and $_SESSION['innit_logout'] === true) {
            // RP-initiated logout
            unset($_SESSION['innit_logout']);
            $oidc->signOut($oidc->getIdToken(), null);
            return;
        }

        try {
            $common_name = $oidc->requestUserInfo('sub');
            // login user by common_name
            Auth::login(\App\User::select('id')->where('common_name', $common_name)->firstOrFail(), true);
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
            redirect($redirect);
        }
    }

    public function backchannelLogout(Request $request): void
    {
        dump($request->all());
    }
}