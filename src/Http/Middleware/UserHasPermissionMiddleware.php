<?php

declare(strict_types=1);

namespace Fifthgate\Objectivity\Users\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Illuminate\Http\Request;

class UserHasPermissionMiddleware extends Middleware
{
    use GeneratesForbiddenResponseTrait;
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param Request $request
     * @return string|null
     */
    //@codeCoverageIgnoreStart
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
    //@codeCoverageIgnoreEnd

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $requestedPermission = reset($guards);
        if (!$request->user()) {
            return $this->generateForbiddenResponse();
        }
        if ($requestedPermission) {
            if (!$request->user()->hasPermission($requestedPermission)) {
                return $this->generateForbiddenResponse();
            }
        }


        return $next($request);
    }


}
