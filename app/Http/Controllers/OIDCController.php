<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Jumbojett\OpenIDConnectClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Jumbojett\OpenIDConnectClientException;

class OIDCController extends Controller
{
    /**
     * Handle OIDC authentication
     */
    public function handle(Request $request): RedirectResponse
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // handle logout-request separately
        if (isset($_SESSION['init_logout']) && $_SESSION['init_logout'] === true) {
            $this->initiateLogout($request);
        }

        $oidc = $this->getOIDCClient();

        if (!$request->has('error')) { // silent authentication with no logged-in user
            $oidc->authenticate(); // authenticates user and saves tokens in instance
            $common_name = $oidc->requestUserInfo('sub');

            // login user by common_name
            /** @var User $user */
            $user = User::select('id')->where('common_name', $common_name)->firstOrFail();
            Auth::login($user, true);

            // store session-id in redis-set
            Redis::sadd('user_sessions:' . $common_name, session_id());
            Redis::sadd('user_sessions:' . $common_name, session()->getId());
            Redis::expire('user_sessions:' . $common_name, config('session.lifetime') * 60);

            LogController::set('ssoLogin'); // set statistics for SSO-authentication
        } else {
            Auth::loginUsingId((config('app.guest_user_id')), true);
            LogController::set('guestLogin'); // set statistics for guest-authentication
        }

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
     * Handle OIDC back channel logout
     * @return JsonResponse
     * @throws OpenIDConnectClientException
     */
    public function backchannelLogout(): JsonResponse
    {
        $oidc = $this->getOIDCClient();

        if (!$oidc->verifyLogoutToken()) return response()->json('Could not verify logout token', 400);

        $common_name = $oidc->getVerifiedClaims('sub');
        $sessionIds = Redis::smembers('user_sessions:' . $common_name);
        Redis::del('user_sessions:' . $common_name);

        Redis::select(config('database.redis.session.database'));

        foreach ($sessionIds as $sessionId) {
            // since we're using both PHP's and Laravel's session-handler, we need to remove both sessions
            Redis::del('PHPREDIS_SESSION:' . $sessionId);
            Redis::del('curriculum' . $sessionId);
        }

        // remove remember token to prevent auto-renew of deleted sessions
        User::where('common_name', $common_name)->update(['remember_token' => null]);

        // if user cannot be found, still return success, or else the IDP will reinitiate logout
        return response()->json('User logged out');
    }

    protected function initiateLogout(Request $request): void
    {
        $oidc = $this->getOIDCClient();
        $oidc->authenticate(); // to load tokens

        // logout user locally
        Auth::guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        session_destroy();

        // RP-initiated logout
        $oidc->signOut($oidc->getIdToken(), null); // calls 'exit;' internally to stop further execution
    }

    protected function getOIDCClient(): OpenIDConnectClient
    {
        return new OpenIDConnectClient(
            config('app.oidc_host'),
            config('app.oidc_client_id'),
            config('app.oidc_client_secret')
        );
    }
}