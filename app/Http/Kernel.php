<?php

namespace App\Http;

use App\Http\Middleware\Admin;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\AuthGates;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\Simulate;
use App\Http\Middleware\TrimStrings;
use App\Http\Middleware\TrustProxies;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;

class Kernel extends HttpKernel
{
    protected $middleware = [
        TrimStrings::class,
        TrustProxies::class,
        ValidatePostSize::class,
        CheckForMaintenanceMode::class,
        ConvertEmptyStringsToNull::class,
    ];

    protected $middlewareGroups = [
        'api' => [
            //'throttle:60,1',
            'bindings',
        ],
        'web' => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            AuthGates::class,
            SetLocale::class,
        ],
        'client_credentials' => [
            CheckClientCredentials::class,
            //'throttle:60,1',
            'bindings',
        ],
        'saml' => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
        ],

    ];

    protected $routeMiddleware = [
        'can'           => Authorize::class,
        // 'auth'          => \Illuminate\Auth\Middleware\Authenticate::class, // default authentication
        'auth'          => Authenticate::class, // custom authentication-handler
        'guest'         => RedirectIfAuthenticated::class,
        'signed'        => ValidateSignature::class,
        'throttle'      => ThrottleRequests::class,
        'cache.headers' => SetCacheHeaders::class,
        'bindings'      => SubstituteBindings::class,
        'auth.basic'    => AuthenticateWithBasicAuth::class,
        'admin'         => Admin::class,
        'simulate'      => Simulate::class,
    ];
}
