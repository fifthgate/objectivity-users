<?php

namespace Fifthgate\Objectivity\Users\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Fifthgate\Objectivity\Users\Domain\User;
use Fifthgate\Objectivity\Users\Domain\LaravelUser;

use Fifthgate\Objectivity\Users\Service\Interfaces\UserServiceInterface;
use Illuminate\Support\Facades\Hash;
use DateTime;
use Fifthgate\Objectivity\Users\Domain\Collection\Interfaces\UserRoleCollectionInterface;
use Fifthgate\Objectivity\Users\Domain\Collection\UserRoleCollection;
use Fifthgate\Objectivity\Users\Domain\ValueObjects\UserRole;
use Fifthgate\Objectivity\Users\Tests\ObjectivityUsersTestCase;

class UserObjectTest extends ObjectivityUsersTestCase
{
    public function testUser(): void
    {
        $roles = new UserRoleCollection();
        $role = $this->userService->getRoles()->getRoleByName("registered-user");
        $this->assertNull($this->userService->getRoles()->getRoleByName("governmentSwami"));
        $roles->add($role);
        $hashedPassword = Hash::make("LoremIpsum");
        $createdAt = new DateTime();
        $user = new LaravelUser(
            "lipsum@lipsum.com",
            "Laura Ipsum"
        );
        $user->setID(1);
        $user->setPassword($hashedPassword);
        $user->setRememberToken("rememberToken");
        $user->setName("Laura Ipsum");
        $user->setCreatedAt($createdAt);
        $user->setUpdatedAt($createdAt);
        $user->setEmailAddress("lipsum@lipsum.com");
        $user->setCookieAcceptanceStatus(true);
        $user->setAPIToken('LoremIpsum');

        $optOutToken = $this->userService->generateOptOutToken();
        $user->setOptOutToken($optOutToken);
        //Test failure condition.
        $this->assertEquals($optOutToken, $user->getOptOutToken());
        $this->assertTrue($user->getCookieAcceptanceStatus());
        $this->assertEquals('LoremIpsum', $user->getAPIToken());
        $this->assertFalse($user->hasRole("admin"));
        $user->setRoles($roles);
        $this->assertTrue($user->hasRole("registered-user"));
        $this->assertEquals(1, $user->getID());
        $this->assertFalse($user->getIsActivated());
        $user->setIsActivated(true);
        $this->assertTrue($user->getIsActivated());
        $this->assertEquals(1, $user->getAuthIdentifier());
        $this->assertEquals($user->getPassword(), $hashedPassword);
        $this->assertEquals("rememberToken", $user->getRememberToken());
        $this->assertEquals("Laura Ipsum", $user->getName());
        $this->assertEquals($user->getCreatedAt(), $createdAt);
        $this->assertTrue($user->hasPermission("viewOwnAccount"));
        $this->assertFalse($user->hasPermission("ruleTheEntireUniverseWithAnIronFist"));
        $this->assertEquals($user->getUpdatedAt(), $createdAt);
        $this->assertEquals("lipsum@lipsum.com", $user->getEmailAddress());
        $this->assertEquals("id", $user->getAuthIdentifierName());
        $this->assertEquals($user->getAuthPassword(), $hashedPassword);
        $this->assertInstanceOf(UserRoleCollectionInterface::class, $user->getRoles());
        $this->assertEquals('remember_token', $user->getRememberTokenName());
        $this->assertEquals($user->getEmailForPasswordReset(), $user->getEmailAddress());

        $user->setBanned(true);
        $this->assertTrue($user->isBanned());
    }

    public function testAllPermissions()
    {
        $permissions = $this->userService->getRoles()->getAllPermissions();
        $this->assertTrue(is_array($permissions));
    }

    public function testUserSerialization()
    {
        $roles = new UserRoleCollection();
        $role = $this->userService->getRoles()->getRoleByName("registered-user");
        $roles->add($role);
        $hashedPassword = Hash::make("LoremIpsum");
        $createdAt = new DateTime();
        $user = new LaravelUser(
            "lipsum@lipsum.com",
            "Laura Ipsum",
            true
        );
        $user->setID(1);
        $user->setPassword($hashedPassword);
        $user->setRememberToken("rememberToken");
        $user->setName("Laura Ipsum");
        $user->setCreatedAt($createdAt);
        $user->setUpdatedAt($createdAt);
        $user->setEmailAddress("lipsum@lipsum.com");
        $user->setCookieAcceptanceStatus(true);
        $user->setAPIToken('LoremIpsum');
        $optOutToken = $this->userService->generateOptOutToken();
        $user->setOptOutToken($optOutToken);

        $output = $user->jsonSerialize();

        $this->assertFalse(isset($output['password']));
        $this->assertFalse(isset($output['remember_token']));
    }
}
