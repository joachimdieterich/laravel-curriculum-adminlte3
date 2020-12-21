@extends('layouts.app')
@section('content')
<body class="hold-transition login-page">

<div class="login-box">
    <div class="login-logo m-1 ">
         <svg class="mt-1" version="1.0" xmlns="http://www.w3.org/2000/svg"
              width="30.000000pt" viewBox="0 0 400.000000 460.000000"
              preserveAspectRatio="xMidYMid meet">
             <g transform="translate(0.000000,460.000000) scale(0.100000,-0.100000)"
                fill="#fff" stroke="none">
                 <path d="M2231 4531 c-8 -5 -48 -74 -90 -153 l-76 -143 -60 -13 c-153 -32
                       -325 -76 -380 -96 l-60 -21 -135 87 c-156 102 -173 105 -259 57 -201 -114
                       -482 -343 -498 -407 -3 -14 11 -78 36 -161 23 -75 43 -146 43 -157 1 -11 -28
                       -65 -64 -119 -36 -55 -94 -152 -128 -215 -34 -63 -65 -119 -70 -123 -4 -5 -70
                       -22 -146 -38 -177 -37 -189 -42 -207 -87 -23 -59 -55 -207 -71 -332 -19 -145
                       -30 -352 -19 -366 4 -6 71 -43 148 -83 77 -40 145 -78 151 -84 6 -7 22 -73 37
                       -147 14 -74 41 -182 60 -240 18 -59 30 -110 25 -115 -5 -6 -47 -68 -93 -140
                       -61 -93 -85 -139 -85 -161 0 -65 203 -365 350 -516 69 -72 78 -78 115 -78 22
                       0 90 15 150 34 177 54 158 55 262 -13 50 -34 140 -87 200 -120 157 -86 149
                       -76 178 -220 40 -196 40 -197 83 -214 52 -21 250 -64 351 -76 119 -15 354 -24
                       367 -14 7 4 46 71 88 148 42 77 83 145 92 151 8 6 38 15 67 18 66 9 228 49
                       334 83 l82 27 138 -92 c76 -51 149 -92 162 -92 77 0 530 330 576 421 23 43 18
                       91 -21 221 -19 66 -36 127 -36 136 -1 9 24 55 55 101 55 82 69 126 49 157 -9
                       15 -406 254 -421 254 -6 0 -50 23 -98 50 -104 60 -134 63 -171 18 -57 -67
                       -256 -255 -315 -296 -409 -287 -958 -268 -1349 47 -267 216 -405 461 -439 786
                       -37 344 94 685 360 938 346 329 831 410 1270 210 71 -32 123 -41 147 -25 7 4
                       59 84 116 177 247 402 264 435 240 471 -4 6 -55 37 -114 68 -59 32 -109 65
                       -112 74 -3 9 -19 78 -34 152 -16 75 -34 146 -41 157 -20 39 -182 79 -436 108
                       -145 17 -283 19 -304 6z"/>
             </g>
        </svg>

        <h3 class="font-weight-light text-white"><strong>curriculum</strong>online</h3>
    </div>
    <div class="card">
        <div class="card-body login-card-body">
<!--            <p class="login-box-msg">{{ trans('global.login_text') }}</p>-->
            @if(\Session::has('message'))
                <p class="alert alert-info">
                    {{ \Session::get('message') }}
                </p>
            @endif
            <form action="{{ route('login') }}" method="POST">
                {{ csrf_field() }}
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="{{ trans('global.login_email') }} / {{ trans('global.user_name') }}" name="email">
                           <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="{{ trans('global.login_password') }}" name="password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        @if (env('GUEST_USER') !== null)
                        <a href="/guest" name="login" class="btn btn-primary btn-block btn-flat">{{ trans('global.login_guest') }}</a>
                        @endif
                    </div>
                    <!-- /.col -->
                    <div class="col-6">
                        <button type="submit" name="login" class="btn btn-primary btn-block btn-flat">{{ trans('global.login') }}</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>
            @if ( ( env('SAML2_RLP_IDP_SSO_URL') !== null ) AND ( !empty(env('SAML2_RLP_IDP_SSO_URL')) ) )
                <div class="social-auth-links text-center mb-3">
                    <p>- OR -</p>
                    <a href="{{ route('saml2_login', ['idpName' => config('saml2_settings.idpNames')[0]]) }}" class="btn btn-block btn-primary">
                        <i class=" mr-2"></i> {{ trans('global.login_SSO') }}
                    </a>
                </div>
            @endif

            <p class="mb-1">
                <a class="" href="{{ route('password.request') }}">
                    {{ trans('global.forgot_password') }}
                </a>
            </p>
            <p class="mb-0">

            </p>
            <p class="mb-1">

            </p>
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
</body>
@endsection
@section('styles')
<style>
    body{
        background-color: #597dff !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100%25' height='100%25' viewBox='0 0 1200 800'%3E%3Cdefs%3E%3CradialGradient id='a' cx='0' cy='800' r='800' gradientUnits='userSpaceOnUse'%3E%3Cstop offset='0' stop-color='%234295ff'/%3E%3Cstop offset='1' stop-color='%234295ff' stop-opacity='0'/%3E%3C/radialGradient%3E%3CradialGradient id='b' cx='1200' cy='800' r='800' gradientUnits='userSpaceOnUse'%3E%3Cstop offset='0' stop-color='%23527aff'/%3E%3Cstop offset='1' stop-color='%23527aff' stop-opacity='0'/%3E%3C/radialGradient%3E%3CradialGradient id='c' cx='600' cy='0' r='600' gradientUnits='userSpaceOnUse'%3E%3Cstop offset='0' stop-color='%233b94ff'/%3E%3Cstop offset='1' stop-color='%233b94ff' stop-opacity='0'/%3E%3C/radialGradient%3E%3CradialGradient id='d' cx='600' cy='800' r='600' gradientUnits='userSpaceOnUse'%3E%3Cstop offset='0' stop-color='%23597dff'/%3E%3Cstop offset='1' stop-color='%23597dff' stop-opacity='0'/%3E%3C/radialGradient%3E%3CradialGradient id='e' cx='0' cy='0' r='800' gradientUnits='userSpaceOnUse'%3E%3Cstop offset='0' stop-color='%232bb8ff'/%3E%3Cstop offset='1' stop-color='%232bb8ff' stop-opacity='0'/%3E%3C/radialGradient%3E%3CradialGradient id='f' cx='1200' cy='0' r='800' gradientUnits='userSpaceOnUse'%3E%3Cstop offset='0' stop-color='%234a77ff'/%3E%3Cstop offset='1' stop-color='%234a77ff' stop-opacity='0'/%3E%3C/radialGradient%3E%3C/defs%3E%3Crect fill='url(%23a)' width='1200' height='800'/%3E%3Crect fill='url(%23b)' width='1200' height='800'/%3E%3Crect fill='url(%23c)' width='1200' height='800'/%3E%3Crect fill='url(%23d)' width='1200' height='800'/%3E%3Crect fill='url(%23e)' width='1200' height='800'/%3E%3Crect fill='url(%23f)' width='1200' height='800'/%3E%3C/svg%3E") !important;
        background-attachment: fixed !important;
        background-size: cover !important;
    }

    .center-icon{
        display:inline-block;
        font-size: 40px;
        line-height: 50px;
        color:white;
        width: 50px;
        height: 50px;
        text-align: center;
        vertical-align: bottom;
    }
</style>

@endsection
