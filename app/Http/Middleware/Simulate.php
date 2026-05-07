<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Simulate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        $request->validate([
            'common_name' => 'required|string|exists:users,common_name',
        ]);

        $commonName = $request->input('common_name');

        Auth::login(User::where('common_name', $commonName)->firstOrFail(), true);

        return $next($request);
    }
}
