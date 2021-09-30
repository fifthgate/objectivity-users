<?php

namespace Fifthgate\Objectivity\Users\Tests\Functional;

use Fifthgate\Objectivity\Users\Tests\ObjectivityUsersTestCase;
use Illuminate\Http\Request;
use Fifthgate\Objectivity\Users\Http\Middleware\RBACAuthenticateMiddleware;
use Illuminate\Contracts\Auth\Factory as Auth;
use Fifthgate\Objectivity\Users\Domain\Collection\UserRoleCollection;

class MiddlewareTest extends ObjectivityUsersTestCase
{
    public function testNotLoggedIn()
    {
        $request = new Request;

        $middleware = new RBACAuthenticateMiddleware($this->app->get(Auth::class));
        $this->assertEquals(403, $middleware->handle($request, function () {
        })->getStatusCode());
    }

    public function testLoggedInWithDefaultPermissions()
    {
        $request = new Request;
        $user = $this->generateTestUser();
        $user = $this->userService->save($user);
        $request->merge(['user' => $user ]);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        $middleware = new RBACAuthenticateMiddleware($this->app->get(Auth::class));

        //If the middleware bounces this request, the below, apparently nonsensical assertion, will never happen.
        $middleware->handle($request, function () {
            $this->assertTrue(true);
        });

        //Test use has a specific permission.
        $middleware->handle($request, function () {
            $this->assertTrue(true);
        }, "viewOwnAccount");
    }

    public function testLoggedInWithInsufficientPermissions()
    {
        $request = new Request;
        $roles = new UserRoleCollection;
        $roles->add($this->userService->getRoles()->getRoleByName('registered-user'));
        $user = $this->generateTestUser(['roles' => $roles]);
        $user = $this->userService->save($user);

        
        $request->merge(['user' => $user ]);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $middleware = new RBACAuthenticateMiddleware($this->app->get(Auth::class));
                $this->assertEquals(403, $middleware->handle($request, function () {
        }, "blowUpTheWorld")->getStatusCode());
    }
}
