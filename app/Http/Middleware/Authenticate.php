<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Log;
class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {

        if (! $request->expectsJson()) {
            return route('login');
        }
    }

    protected function unauthenticated($request, array $guards)
    {
        Log:info('Bearer Token Invalid');
        abort(response()->json(
            [
                'error' => 'unauthenticated',
                'message' => 'Barer token invalid or expired',
            ], 401));
    }
}
