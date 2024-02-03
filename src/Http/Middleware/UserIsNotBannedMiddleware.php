<?php

namespace Fifthgate\Objectivity\Users\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Response;
use Illuminate\Contracts\Auth\Factory as Auth;
use Fifthgate\Objectivity\Users\Service\Interfaces\UserServiceInterface;

class UserIsNotBannedMiddleware extends Middleware
{
    protected UserServiceInterface $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    //@codeCoverageIgnoreStart
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
    //@codeCoverageIgnoreEnd

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
        $user = $request->user();
        $banned = $this->userService->emailIsBanned($user->getEmailAddress());

        if ($banned) {
            $banReason = $this->userService->getBanReason($user->getEmailAddress());
            return response()->json(["message" => "Forbidden. Your user account has been banned due to {$banReason}."], 403);
        }
        return $next($request);
    }
}
