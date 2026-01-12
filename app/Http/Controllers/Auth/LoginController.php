<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LogController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function showLoginForm(Request $request)
    {
        return env('APP_ENV') == 'local'
            ? $this->localLogin($request) // in local environment, show login form
            : redirect('/home'); // in live environment, redirect to /home to trigger SSO through Auth-middleware
    }

    /**
     * Show the application's login form for local auth
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function localLogin(Request $request)
    {
        return view('auth.login');
    }

    public function localLogout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return view('auth.login');
    }

    /**
     * Enable Login with email or username
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $input = $request->all();

        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        if (auth()->attempt([$fieldType => $input['email'], 'password' => $input['password']])) {
            LogController::set('login');
            LogController::set('activeOrg', auth()->user()->current_organization_id);

            return redirect()->intended('home');
        } else {
            return redirect()->route('login')
                ->with('error', 'Email-Address And Password Are Wrong.');
        }
    }

    /**
     * Overwrite Logout
     * If SSO is set add sessionIndex and nameId to request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        if (env('APP_ENV') == 'local') // in local environment, logout user and redirect to login-page
        {
            return $this->localLogout($request);
        }
        else // in live environment, redirect to SSO logout
        {
            $oidc = new \Jumbojett\OpenIDConnectClient(
                env('OIDC_RLP_IDP_HOST'),
                env('OIDC_CLIENT_ID'),
                env('OIDC_CLIENT_SECRET')
            );

            // except if authenticated as guest user, then redirect to SSO login
            if (auth()->user()->id == env('GUEST_USER'))
            {
                $oidc->authenticate();
            }
            else
            {
                $oidc->signOut($oidc->getIdToken(), null);
            }
        }
    }
}