<?php

namespace Fifthgate\Objectivity\Users\Tests\Unit;

use Fifthgate\Objectivity\Users\Tests\ObjectivityUsersTestCase;
use Fifthgate\Objectivity\Users\Domain\Collection\UserRoleCollection;
use Fifthgate\Objectivity\Users\Domain\ValueObjects\UserRole;

class UserRoleCollectionTest extends ObjectivityUsersTestCase
{
    public function testRoleCollectionIntegrity()
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

        $moderatorRoleMachineName = "tx";
        $moderatorRoleName ="T-X";
        $moderatorRoleDescription = "A bit like a T-1000, but lamer. A bit like a T-800, but lamer. But it's a go-go gadget lady this time!";
        $moderatorRolePermissions = ["search","terminate","gratuitouslyInflateCleavage"];

        $moderatorRole = new UserRole(
            $moderatorRoleMachineName,
            $moderatorRoleName,
            $moderatorRoleDescription,
            $moderatorRolePermissions
        );

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

        $roles = new UserRoleCollection;
        $roles->add($userRole);
        $roles->add($moderatorRole);
        $roles->add($adminRole);

        $this->assertEquals($userRole, $roles->getRoleByName('t800'));

        $this->assertNull($roles->getRoleByName('t600'));

        $allPermissions = [
            "search" => "search",
            "terminate" => "terminate",
            "wearSunglassesAtInappropriateTimes" => "wearSunglassesAtInappropriateTimes",
            "gratuitouslyInflateCleavage" => "gratuitouslyInflateCleavage"
        ];
        $this->assertEquals($allPermissions, $roles->getAllPermissions());

        $this->assertEquals(2, $roles->filterByMachineNames(["t800", "tx"])->count());
    }
}
