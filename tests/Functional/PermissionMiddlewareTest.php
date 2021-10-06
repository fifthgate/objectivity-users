<?php

namespace Fifthgate\Objectivity\Users\Tests\Functional;

use Fifthgate\Objectivity\Users\Tests\ObjectivityUsersTestCase;
use Illuminate\Http\Request;
use Fifthgate\Objectivity\Users\Http\Middleware\UserHasPermissionMiddleware;
use Fifthgate\Objectivity\Users\Http\Middleware\UserHasRoleMiddleware;
use Illuminate\Contracts\Auth\Factory as Auth;
use Fifthgate\Objectivity\Users\Domain\Collection\UserRoleCollection;

class PermissionMiddlewareTest extends ObjectivityUsersTestCase
{
    public function testNotLoggedIn()
    {
        $request = new Request;

        $middleware = new UserHasPermissionMiddleware($this->app->get(Auth::class));
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
        $middleware = new UserHasPermissionMiddleware($this->app->get(Auth::class));

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

        $middleware = new UserHasPermissionMiddleware(
            $this->app->get(Auth::class)
        );
        $this->assertEquals(403, $middleware->handle(
            $request,
            function () {
                return response()->json(["message" => "OK"], 200);
            },
            "blowUpTheWorld"
        )->getStatusCode());
    }

    public function testUserHasRoleMiddleware()
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
        $middleware = new UserHasRoleMiddleware(
            $this->app->get(Auth::class)
        );
        $this->assertEquals(200, $middleware->handle(
            $request,
            function () {
                return response()->json(["message" => "OK"], 200);
            },
            "registered-user"
        )->getStatusCode());

        $this->assertEquals(403, $middleware->handle(
            $request,
            function () {
                return response()->json(["message" => "OK"], 200);
            },
            "admin"
        )->getStatusCode());
    }
}
