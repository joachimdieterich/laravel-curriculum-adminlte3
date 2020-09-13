<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Aacotroneo\Saml2\Saml2Auth;
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
     * Overwrite Logout
     * If SSO is set add sessionIndex and nameId to request
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        if (( env('SAML2_RLP_IDP_SSO_URL') !== null ) AND ( !empty(env('SAML2_RLP_IDP_SSO_URL')) ) )
        {
            return redirect()->action("\Aacotroneo\Saml2\Http\Controllers\Saml2Controller@logout",
                [
                    'idpName'       => 'rlp', //todo: add use dynamic value (env?)
                    'returnTo'      => env('SAML2_RLP_IDP_HOST'), //$request->query('returnTo'),
                    'sessionIndex'  => $request->session()->get('sessionIndex'),
                    'nameId'        => $request->session()->get('nameId'),
                ]);
        }
        else
        {
            $this->guard()->logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            return $this->loggedOut($request) ?: redirect('/');
        }

    }
}
