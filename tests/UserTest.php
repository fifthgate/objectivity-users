<?php

namespace Fifthgate\Objectivity\Users\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Fifthgate\Objectivity\Users\Domain\User;
use Fifthgate\Objectivity\Users\Service\Interfaces\UserServiceInterface;
use Illuminate\Support\Facades\Hash;
use \DateTime;
use Fifthgate\Objectivity\Users\Domain\Collection\Interfaces\UserRoleCollectionInterface;
use Fifthgate\Objectivity\Users\Domain\Collection\UserRoleCollection;
use Fifthgate\Objectivity\Users\Domain\ValueObjects\UserRole;
use Fifthgate\Objectivity\Users\Tests\ObjectivityUsersTestCase;

class UserTest extends ObjectivityUsersTestCase
{
    public function testRoleConstruction()
    {
        $roleMachineName = "t800";
        $roleName = "Cyberdyne Systems T-800 model 101";
        $roleDescription = "It can't be bargained with. It can't be reasoned with. It doesn't feel pity, or remorse, or fear! And it absolutely will not stop, ever, until you are dead!";
        $rolePermissions = ["search","terminate","wearSunglassesAtInappropriateTimes"];

        $userRole = new UserRole(
            $roleMachineName,
            $roleName,
            $roleDescription,
            $rolePermissions
        );
        $this->assertEquals($userRole->getMachineName(), $roleMachineName);
        $this->assertEquals($userRole->getName(), $roleName);
        $this->assertEquals($userRole->getDescription(), $roleDescription);
        foreach ($rolePermissions as $rolePermission) {
            $this->assertTrue($userRole->hasPermission($rolePermission));
        }
        $this->assertEquals($userRole->getPermissions(), $rolePermissions);
        $adminRoleMachineName = "t1000";
        $adminRoleName = "T 1000";
        $adminRoleDescription = "Advanced prototype. Made of mimetic polyalloy";
        $adminRolePermissions = ["*"];
        $adminRole = new UserRole(
            $adminRoleMachineName,
            $adminRoleName,
            $adminRoleDescription,
            $adminRolePermissions
        );

        $this->assertEquals($adminRole->getMachineName(), $adminRoleMachineName);
        $this->assertEquals($adminRole->getName(), $adminRoleName);
        $this->assertEquals($adminRole->getDescription(), $adminRoleDescription);
        $this->assertEquals($adminRole->getID(), 0);
        foreach ($adminRolePermissions as $rolePermission) {
            $this->assertTrue($adminRole->hasPermission($rolePermission));
        }
    }

    public function testUser()
    {
        $roles = new UserRoleCollection;
        $role = $this->userService->getRoles()->getRoleByName("listWriter");
        $this->assertNull($this->userService->getRoles()->getRoleByName("governmentSwami"));
        $roles->add($role);
        $hashedPassword = Hash::make("LoremIpsum");
        $createdAt = new DateTime;
        $user = new User;
        $user->setID(1);
        $user->setInitials('WTF');
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
        $this->assertEquals('WTF', $user->getInitials());
        $this->assertFalse($user->hasRole("admin"));
        $user->setRoles($roles);
        $this->assertTrue($user->hasRole("listWriter"));
        $this->assertEquals($user->getID(), 1);
        $this->assertFalse($user->getIsActivated());
        $user->setIsActivated(true);
        $this->assertTrue($user->getIsActivated());
        $this->assertEquals($user->getAuthIdentifier(), 1);
        $this->assertEquals($user->getPassword(), $hashedPassword);
        $this->assertEquals($user->getRememberToken(), "rememberToken");
        $this->assertEquals($user->getName(), "Laura Ipsum");
        $this->assertEquals($user->getCreatedAt(), $createdAt);
        $this->assertTrue($user->hasPermission("createShoppingList"));
        $this->assertFalse($user->hasPermission("ruleTheEntireUniverseWithAnIronFist"));
        $this->assertEquals($user->getUpdatedAt(), $createdAt);
        $this->assertEquals($user->getEmailAddress(), "lipsum@lipsum.com");
        $this->assertEquals($user->getAuthIdentifierName(), "id");
        $this->assertEquals($user->getAuthPassword(), $hashedPassword);
        $this->assertInstanceOf(UserRoleCollectionInterface::class, $user->getRoles());
        $this->assertEquals($user->getRememberTokenName(), 'remember_token');
        $this->assertEquals($user->name, $user->getName());
        $this->assertEquals($user->email, $user->getEmailAddress());
        $this->assertEquals($user->password, $user->getPassword());
        $this->assertEquals($user->initials, $user->getInitials());
        $this->assertNull($user->roles);
        $this->assertEquals($user->getEmailForPasswordReset(), $user->getEmailAddress());
    }

    public function testAllPermissions()
    {
        $permissions = $this->userService->getRoles()->getAllPermissions();
        $this->assertTrue(is_array($permissions));
    }
}
