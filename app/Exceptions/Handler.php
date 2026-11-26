<?php

namespace App\Exceptions;

use App\Services\Authenticator;
use BigBlueButton\Exceptions\NetworkException;
use EduSharingApiClient\AppAuthException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Jumbojett\OpenIDConnectClientException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        OpenIDConnectClientException::class,
        AppAuthException::class,
        NetworkException::class,
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $t) {
            Authenticator::authenticate(request());
        });
    }
}
