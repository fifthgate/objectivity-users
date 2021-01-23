<?php

namespace Fifthgate\Objectivity\Users\Tests;

use Orchestra\Testbench\TestCase;
use Fifthgate\Objectivity\Users\Domain\User;
use Fifthgate\Objectivity\Users\UserServiceProvider;
use Fifthgate\Objectivity\Users\Service\Interfaces\UserServiceInterface;
use Illuminate\Support\Facades\Hash;
use \DateTime;
use Illuminate\Support\Str;

abstract class ObjectivityUsersTestCase extends TestCase {

	protected function generateTestUser(array $overrides = [])
    {
        $user = new User;
        $user->setPassword(Hash::make($overrides["password"] ?? 'LoremIpsum'));
        $user->setName($overrides["name"] ?? 'Laura Ipsum');
        $user->setEmailAddress($overrides["email"] ?? 'lipsum@lauraipsum.com');
        $user->setCreatedAt($overrides["createdAt"] ?? new DateTime);
        $user->setUpdatedAt($overrides["updatedAt"] ?? new DateTime);
        $user->setRoles($overrides["roles"] ?? $this->userService->getRoles());
        $user->setIsActivated($overrides["is_activated"] ?? false);
        $user->setCookieAcceptanceStatus($overrides["cookie_acceptance_status"] ?? false);
        $user->setAPIToken($overrides["api_token"] ?? Str::random(60));
        return $user;
    }

  	protected function getPackageProviders($app) {
	    return [
	    	UserServiceProvider::class
	    ];
	}

	protected function getEnvironmentSetUp($app)
	{
		$app['config']->set('key', 'base64:j84cxCjod/fon4Ks52qdMKiJXOrO5OSDBpXjVUMz61s=');
	    // Setup default database to use sqlite :memory:
	    $app['config']->set('database.default', 'testbench');
	    $app['config']->set('database.connections.testbench', [
	        'driver'   => 'sqlite',
	        'database' => ':memory:',
	        'prefix'   => '',
	    ]);
	}

	/**
	 * Setup the test environment.
	 */
	protected function setUp(): void {
	    parent::setUp();
	    $this->userService = $this->app->get(UserServiceInterface::class);
	    $this->loadLaravelMigrations();
	    $this->loadMigrationsFrom(__DIR__ . '/migrations');
	    $this->artisan('migrate', ['--database' => 'testbench'])->run();
	}
}