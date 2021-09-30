<?php

namespace Fifthgate\Objectivity\Users\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Response;
use Illuminate\Contracts\Auth\Factory as Auth;

class RBACAuthenticateMiddleware extends Middleware
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

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $requestedPermission = reset($guards);
        if (!$request->user() or !$request->user()->hasPermission($requestedPermission)) {
            return response()->json(["message" => "access_denied"], 403);
        }

        return $next($request);
    }
}
