<?php

namespace Fifthgate\Objectivity\Users\Tests;

use Orchestra\Testbench\TestCase;

use Fifthgate\Objectivity\Users\UserServiceProvider;


abstract class ObjectivityUsersTestCase extends TestCase {

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
	    //$app['config']->set('smartmenu', $this->testMenuArray);
	}

	/**
	 * Setup the test environment.
	 */
	protected function setUp(): void {
	    parent::setUp();
		//$this->menuService = $this->app->get(MenuServiceInterface::class);
	}
}