@extends('layouts.app')
@section('content')
<div class="login-box">
    <div class="login-logo m-1">
         {{ trans('global.site_title') }}
    </div>
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">{{ trans('global.login_text') }}</p>
            @if(\Session::has('message'))
                <p class="alert alert-info">
                    {{ \Session::get('message') }}
                </p>
            @endif
            <form action="{{ route('login') }}" method="POST">
                {{ csrf_field() }}
                <div class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="{{ trans('global.login_email') }}" name="email">
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
            <div class="social-auth-links text-center mb-3">
                <p>- OR -</p>
                <a href="{{ route('saml2_login', ['idpName' => config('saml2_settings.idpNames')[0]]) }}" class="btn btn-block btn-primary">
                    <i class=" mr-2"></i> {{ trans('global.login_SSO') }}
                </a>
            </div>


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
@endsection