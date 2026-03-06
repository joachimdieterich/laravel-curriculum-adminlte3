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
    public function handle(Request $request): \Illuminate\Http\RedirectResponse
    {
        
        // handle logout-request separately
        if (session('init_logout') === true) $this->initiateLogout($request);

        $oidc = new OpenIDConnectClient(
            env('OIDC_RLP_IDP_HOST'),
            env('OIDC_CLIENT_ID'),
            env('OIDC_CLIENT_SECRET')
        );

        if (!$request->has('error')) { // silent authentication with no logged-in user
            $oidc->authenticate(); // authenticates user and saves tokens in instance
            $common_name = $oidc->requestUserInfo('sub');
            // login user by common_name
            Auth::login(\App\User::select('id')->where('common_name', $common_name)->firstOrFail(), true);
    
            // store session-id in redis-set
            $sessionId = session()->getId();
            Redis::sadd('user_sessions:' . $common_name, $sessionId);
            Redis::expire('user_sessions:' . $common_name, config('session.lifetime') * 60);
    
            LogController::set('ssoLogin'); // set statistics for SSO-authentication
        } else {
            Auth::loginUsingId((env('GUEST_USER')), true);
            LogController::set('guestLogin'); // set statistics for guest-authentication
        }

        LogController::setStatistics(); // set statistics for used browser and device type

        $redirect = '/home'; // fallback
        // since the user got redirected back after authentication, redirect to the originally requested URL
        if (session('redirect_to')) {
            $redirect = session('redirect_to');
            session()->forget('redirect_to');
        }

        return redirect($redirect);
    }

    /**
     * Handle OIDC backchannel logout
     * @param  \Illuminate\Http\Request  $request contains logout_token
     * @return \Illuminate\Http\Response
     */
    public function backchannelLogout(Request $request): \Illuminate\Http\JsonResponse
    {
        $oidc = new OpenIDConnectClient(
            env('OIDC_RLP_IDP_HOST'),
            env('OIDC_CLIENT_ID'),
            env('OIDC_CLIENT_SECRET')
        );

        if (!$oidc->verifyLogoutToken()) return response()->json('Could not verify logout token', 400);

        $common_name = $oidc->getVerifiedClaims('sub');
        $sessionIds = Redis::smembers('user_sessions:' . $common_name);

        foreach ($sessionIds as $sessionId) {
            Redis::del('curriculum_cache' . $sessionId);
        }

        Redis::del('user_sessions:' . $common_name);
        // remove remember token to prevent auto-renew of deleted sessions
        \User::where('common_name', $common_name)->update(['remember_token' => null]);
            
        // if user cannot be found, still return success, or else the IDP will reinitiate logout
        return response()->json('User logged out', 200);
    }

    protected function initiateLogout(Request $request): never
    {
        session()->forget('init_logout');

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