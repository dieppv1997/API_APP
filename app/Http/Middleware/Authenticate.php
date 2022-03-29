<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Middleware;

use App\Exceptions\CheckAuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param Request $request
     * @return void
     * @throws CheckAuthenticationException
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            throw new CheckAuthenticationException();
        }
    }
}
