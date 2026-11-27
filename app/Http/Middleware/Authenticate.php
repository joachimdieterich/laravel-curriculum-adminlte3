<?php

namespace App\Http\Middleware;

use App\Services\Authenticator;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        Authenticator::authenticate($request);

        return $next($request);
    }
}
