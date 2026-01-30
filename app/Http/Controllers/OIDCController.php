<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Jumbojett\OpenIDConnectClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class OIDCController extends Controller
{
    /**
     * Handle OIDC authentication
     */
    public function handle(Request $request)
    {
        // handle logout-request separately
        if (isset($_SESSION['init_logout']) and $_SESSION['init_logout'] === true) $this->initiateLogout($request);

        $oidc = new OpenIDConnectClient(
            env('OIDC_RLP_IDP_HOST'),
            env('OIDC_CLIENT_ID'),
            env('OIDC_CLIENT_SECRET')
        );

        if (isset($_SESSION['redirect_to'])) {
            $allow_guest = $request->has('sharing_token')
                or str_starts_with($request->getRequestUri(), '/navigator')
                or str_starts_with($request->getRequestUri(), '/eventSubscriptions')
                or str_ends_with($request->getPathInfo(), 'startWithPw'); // videoconference-link;
            // if resource is accessible for guests, request silent authentication
            if ($allow_guest) $oidc->addAuthParam(['prompt' => 'none']);
        }
        
        try {
            $oidc->authenticate(); // authenticates user and saves tokens in instance
        } catch (\Throwable $th) {
            // if authentication fails, login as guest user
            Auth::loginUsingId((env('GUEST_USER')), true);
            LogController::set('guestLogin'); // set statistics for guest-authentication
        }

        $common_name = $oidc->requestUserInfo('sub');
        // login user by common_name
        Auth::login(\App\User::select('id')->where('common_name', $common_name)->firstOrFail(), true);
        // store session-id in redis-set
        $sessionId = session()->getId();
        Redis::sadd('user_sessions:' . $common_name, $sessionId);
        Redis::expire('user_sessions:' . $common_name, config('session.lifetime') * 60);

        LogController::set('ssoLogin'); // set statistics for SSO-authentication
        LogController::setStatistics(); // set statistics for used browser and device type

        $redirect = '/home'; // fallback
        // since the user got redirected back after authentication, redirect to the originally requested URL
        if (isset($_SESSION['redirect_to'])) {
            $redirect = $_SESSION['redirect_to'];
            unset($_SESSION['redirect_to']);
        }
        return redirect($redirect);
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
        $sessionIds = Redis::smembers('user_sessions:' . $common_name);

        foreach ($sessionIds as $sessionId) {
            // TODO: check if session-key prefix is correct
            Redis::del('laravel_session:' . $sessionId);
        }

        Redis::del('user_sessions:' . $common_name);
            
        // if user cannot be found, still return success, or else it will retry sending the logout token
        return response('User logged out', 200);
    }

    protected function initiateLogout(Request $request)
    {
        unset($_SESSION['init_logout']);

        $oidc = new OpenIDConnectClient(
            env('OIDC_RLP_IDP_HOST'),
            env('OIDC_CLIENT_ID'),
            env('OIDC_CLIENT_SECRET')
        );
        $oidc->authenticate(); // to load tokens

        // logout user locally
        Auth::guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // RP-initiated logout
        $oidc->signOut($oidc->getIdToken(), null); // calls 'exit;' internally to stop further execution
    }
}