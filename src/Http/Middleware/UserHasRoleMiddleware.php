<?php

declare(strict_types=1);

namespace Fifthgate\Objectivity\Users\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Illuminate\Http\Request;

class UserHasRoleMiddleware extends Middleware
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
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $roleString = reset($guards);
        if ($roleString) {
            if (strpos($roleString, ",") !== false) {
                $roles = explode(",", $roleString);
            } else {
                $roles = [
                    0 => $roleString
                ];
            }
        } else {
            return $this->generateForbiddenResponse();
        }

        $userHasAtLeastOneRole = false;
        $user = $request->user();

        if ($user) {
            foreach ($roles as $roleName) {
                if ($user->hasRole($roleName)) {
                    $userHasAtLeastOneRole = true;
                }
            }
        }

        if (!$userHasAtLeastOneRole) {
            return $this->generateForbiddenResponse();
        }

        return $next($request);
    }


}
