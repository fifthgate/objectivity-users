<?php

namespace Fifthgate\Objectivity\Users;

use Illuminate\Support\ServiceProvider;
use Fifthgate\Objectivity\Users\Infrastructure\Repository\Interfaces\UserRepositoryInterface;
use Fifthgate\Objectivity\Users\Infrastructure\Repository\UserRepository;
use Fifthgate\Objectivity\Users\Service\Interfaces\UserServiceInterface;
use Fifthgate\Objectivity\Users\Service\UserService;
use Fifthgate\Objectivity\Users\Infrastructure\Mapper\Interfaces\UserMapperInterface;
use Fifthgate\Objectivity\Users\Infrastructure\Mapper\UserMapper;
use Fifthgate\Objectivity\Users\Service\Factories\UserRolesFactory;
use Fifthgate\Objectivity\Users\Domain\Collection\Interfaces\UserRoleCollectionInterface;
use Fifthgate\Objectivity\Users\Infrastructure\Mapper\Interfaces\BannedEmailsMapperInterface;
use Fifthgate\Objectivity\Users\Infrastructure\Mapper\BannedEmailsMapper;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['auth']->provider('userService', function () {
            return $this->app->get(UserServiceInterface::class);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            UserRoleCollectionInterface::class,
            function ($app) {
                $rolesFactory = new UserRolesFactory;
                return $rolesFactory(env('APP_DEBUG', false));
            }
        );
        $this->app->bind(
            UserServiceInterface::class,
            UserService::class
        );
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
        $this->app->bind(
            BannedEmailsMapperInterface::class,
            BannedEmailsMapper::class
        );
        $this->app->bind(
            UserMapperInterface::class,
            UserMapper::class
        );
    }
}
