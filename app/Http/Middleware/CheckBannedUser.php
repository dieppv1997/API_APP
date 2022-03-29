<?php

namespace App\Http\Middleware;

use App\Exceptions\CheckAuthorizationException;
use App\Traits\UserTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBannedUser
{
    use UserTrait;

    /**
     * Handle an incoming request.
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws CheckAuthorizationException
     */
    public function handle(Request $request, Closure $next)
    {
        $currentUser = Auth::user();
        if ($this->isBanned($currentUser)) {
            throw new CheckAuthorizationException();
        }
        return $next($request);
    }
}
