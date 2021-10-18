<?php

namespace Fifthgate\Objectivity\Users\Tests\Functional;

use Fifthgate\Objectivity\Users\Tests\ObjectivityUsersTestCase;
use Fifthgate\Objectivity\Users\Http\Rules\UserRolesExistRule;

class UserRolesExistRuleTest extends ObjectivityUsersTestCase
{
    public function testRule()
    {
        $rule = new UserRolesExistRule($this->userService);
        $validInput = [
            'admin',
            'registered-user'
        ];

        $invalidInput = [
            'nogoodnik',
            'malcontent',
            'saboteur'
        ];
        $this->assertTrue($rule->passes('roles', $validInput));
        $rule->passes('roles', $validInput);
        
        $rule = new UserRolesExistRule($this->userService);
        $this->assertFalse($rule->passes('roles', $invalidInput));
        $expectedErrorString = "The role 'nogoodnik' supplied for the 'roles' field does not exist, The role 'malcontent' supplied for the 'roles' field does not exist, The role 'saboteur' supplied for the 'roles' field does not exist";
        $this->assertEquals($expectedErrorString, $rule->message());
    }

    public function testRuleEdgeCases()
    {
        $rule = new UserRolesExistRule($this->userService);
        $expectedErrorString = "Role input supplied to the 'roles' field must be supplied as an array of role names.";
        $this->assertFalse($rule->passes('roles', 'someWrongValue'));
        $this->assertEquals($expectedErrorString, $rule->message());

        $rule = new UserRolesExistRule($this->userService);
        $this->assertFalse($rule->passes('roles', []));
        $this->assertEquals($expectedErrorString, $rule->message());
    }
}
