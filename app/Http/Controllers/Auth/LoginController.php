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
        $this->middleware('guest')->except('logout');
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
        if (
            (env('SAML2_RLP_IDP_SSO_URL') !== null)
            and (! empty(env('SAML2_RLP_IDP_SSO_URL')))
            and (auth()->user()->id !== env('GUEST_USER'))
        )
        {
            return redirect()->action("\Aacotroneo\Saml2\Http\Controllers\Saml2Controller@logout",
                [
                    'idpName'       => 'rlp', //todo: add use dynamic value (env?)
                    'returnTo'      => $request->query('returnTo'),
                    'sessionIndex'  => $request->session()->get('sessionIndex'),
                    'nameId'        => $request->session()->get('nameId'),
                ]);
        }
        else
        {
            $this->guard()->logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            if (env('BRAND_MENU_HREF_1'))
            {
                return $this->loggedOut($request) ?: redirect(env('BRAND_MENU_HREF_1'));
            }
            else
            {
                return $this->loggedOut($request) ?: redirect('/');
            }
        }
    }
}
