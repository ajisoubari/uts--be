<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

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
            // response no token provided if user is not login
            $response = [
                'meta' => [
                    'message' => 'Error Autentication: No Token Provided!',
                    'code' => '401'
                ]
            ];
            abort(response()->json($response, 401));
        }
    }
}
