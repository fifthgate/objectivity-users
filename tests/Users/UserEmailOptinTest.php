<?php

namespace Tests\Feature\Users;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Fifthgate\Objectivity\Users\Tests\ObjectivityUsersTestCase;
use Fifthgate\Objectivity\Users\Domain\User;

class TestUserEmailOptin extends ObjectivityUsersTestCase
{
    public function testUserOptinIntegrity()
    {
        $user = new User;
        $this->assertFalse($user->getEmailOptIn());
        $user->setEmailOptIn(true);
        $this->assertTrue($user->getEmailOptIn());
    }

    public function testUserOptinSave()
    {
        $user = $this->generateTestUser();
        $this->assertFalse($user->getEmailOptIn());
        $user->setEmailOptIn(true);
        $savedUser = $this->userService->save($user);
        $this->assertTrue($savedUser->getEmailOptIn());
        $foundUser = $this->userService->find($savedUser->getID());
        $this->assertTrue($foundUser->getEmailOptIn());
    }
}
