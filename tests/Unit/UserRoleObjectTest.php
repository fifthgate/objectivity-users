<?php

namespace Fifthgate\Objectivity\Users\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Fifthgate\Objectivity\Users\Domain\User;
use Fifthgate\Objectivity\Users\Domain\LaravelUser;

use Fifthgate\Objectivity\Users\Service\Interfaces\UserServiceInterface;
use Illuminate\Support\Facades\Hash;
use \DateTime;
use Fifthgate\Objectivity\Users\Domain\Collection\Interfaces\UserRoleCollectionInterface;
use Fifthgate\Objectivity\Users\Domain\Collection\UserRoleCollection;
use Fifthgate\Objectivity\Users\Domain\ValueObjects\UserRole;
use Fifthgate\Objectivity\Users\Tests\ObjectivityUsersTestCase;

class UserRoleObjectTest extends ObjectivityUsersTestCase
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
}
