<?php

namespace Fifthgate\Objectivity\Users\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use Fifthgate\Objectivity\Users\Service\Interfaces\UserServiceInterface;
use Fifthgate\Objectivity\Users\Domain\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use \DateTimeInterface;
use Fifthgate\Objectivity\Users\Domain\Collection\Interfaces\UserCollectionInterface;
use Fifthgate\Objectivity\Users\Domain\Interfaces\UserInterface;
use Fifthgate\Objectivity\Users\Tests\ObjectivityUsersTestCase;

class UserServiceTest extends ObjectivityUsersTestCase
{
    public function testUserStores()
    {
        $app = $this->createApplication();
        $user = $this->generateTestUser(["initials" => "WTH"]);
        $user = $this->userService->save($user);
        $this->assertDatabaseHas('users', [
            'email' => 'lipsum@lauraipsum.com',
            'name' => 'Laura Ipsum',
            'initials' => 'WTH'
        ]);
        $this->assertNotNull($user->getID());
    }

    public function testGetEntityInfo()
    {
        $this->assertIsArray($this->userService->getEntityInfo());
        $entityInfo = $this->userService->getEntityInfo();
        $entityInfo = reset($entityInfo);
        $this->assertEquals("User", $entityInfo["name"]);
        $this->assertFalse($entityInfo["softDeletes"]);
        $this->assertFalse($entityInfo["publishes"]);
        $this->assertTrue($entityInfo["timestamps"]);
    }

    public function testGetRandomPassword()
    {
        $string1 = $this->userService->generateRandomPassword(32);
        $string2 = $this->userService->generateRandomPassword(32);
        $this->assertNotEquals($string1, $string2);
        $this->assertEquals(32, strlen($string1));
        $this->assertEquals(32, strlen($string2));
    }

    /**
     * Just tests that hashes are consistent.
     */
    public function testHashPassword()
    {
        $password = "PasSw0rd!";
        $hash1 = $this->userService->hashPassword($password);
        $hash2 = $this->userService->hashPassword($password);
        $this->assertNotEquals($hash1, $hash2);
    }

    public function testRetrieveByID()
    {
        $user = $this->generateTestUser();
        $user = $this->userService->save($user);

        $user = $this->userService->retrieveById($user->getID());
        $this->assertInstanceOf(UserInterface::class, $user);
    }

    public function testRetrieveByCredentials()
    {
        $testStart = new Carbon;
        $user = $this->generateTestUser();
        $user = $this->userService->save($user);
        $id = $user->getID();
        $user = $this->userService->retrieveByCredentials(['email' => $user->getEmailAddress()]);
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertEquals($user->getID(), $id);

        $this->assertNull($this->userService->retrieveByCredentials(['password'=>'something']));
    }

    public function testUpdateRememberToken()
    {
        $testStart = new Carbon;
        $user = $this->generateTestUser();
        $user = $this->userService->save($user);
        $this->userService->updateRememberToken($user, 'rememberToken');
        $user = $this->userService->retrieveById($user->getID());
        $this->assertEquals($user->getRememberToken(), 'rememberToken');
    }

    public function testRetrieveByToken()
    {
        $testStart = new Carbon;
        $user = $this->generateTestUser();
        $user = $this->userService->save($user);
        $id = $user->getID();
        $this->userService->updateRememberToken($user, 'rememberToken');
        $user = $this->userService->retrieveByToken($user->getID(), 'rememberToken');
        $this->assertEquals($user->getID(), $id);
    }

    public function testValidateCredentials()
    {
        $testStart = new Carbon;
        $user = $this->generateTestUser();
        $user = $this->userService->save($user);
        $this->assertTrue($this->userService->validateCredentials($user, ['password'=>'LoremIpsum']));
    }

    public function testFindAll()
    {
        $testStart = new Carbon;
        $user = $this->generateTestUser();
        $user = $this->userService->save($user);
        $users = $this->userService->findAll();
        $this->assertInstanceOf(UserCollectionInterface::class, $users);
        $this->assertFalse($users->isEmpty());
        $foundUser = $users->first();
        $this->assertInstanceOf(UserInterface::class, $foundUser);
        $this->assertEquals($user->getID(), $foundUser->getID());
    }

    public function testDelete()
    {
        $testStart = new Carbon;
        $user = $this->generateTestUser();
        $user = $this->userService->save($user);
        $this->userService->delete($user);
        $this->assertNull($this->userService->retrieveById($user->getID()));
    }

    public function testSaveIntegrity()
    {
        $testPassword = "Test";
        $testName = "Test Name";
        $testEmailAddress = "probity@inaction.gov";
        $createdAt = new Carbon;
        $testRoles = $this->userService->getRoles()->filterByMachineNames(["shopper"]);
        $user = new User;
        $user->setPassword($testPassword);
        $user->setName($testName);
        $user->setEmailAddress($testEmailAddress);
        $user->setRoles($testRoles);
        $user->setIsActivated(true);
        $user->setCookieAcceptanceStatus(false);
        $user->setCreatedAt($createdAt);
        $user->setUpdatedAt($createdAt);
        $user->setInitials("FML");
        $user->setAPIToken('LoremIpsum');
        $optOutToken = $this->userService->generateOptOutToken();
        $user->setOptOutToken($optOutToken);
        $user = $this->userService->save($user);
        $foundUser = $this->userService->find($user->getID());

        //Test failure condition.
        $this->assertEquals($optOutToken, $foundUser->getOptOutToken());
        $this->assertEquals("FML", $foundUser->getInitials());
        $this->assertEquals("LoremIpsum", $foundUser->getAPIToken());
        $this->assertEquals($testPassword, $foundUser->getPassword());
        $this->assertEquals($testRoles->count(), $foundUser->getRoles()->count());
        $this->assertEquals($testEmailAddress, $foundUser->getEmailAddress());
        $this->assertEquals($testName, $foundUser->getName());
        $this->assertFalse($foundUser->getCookieAcceptanceStatus());
        $this->assertTrue($foundUser->getIsActivated());
    }

    public function testBanSystem()
    {
        $bannedEmail = "nogoodnik@shadyrussianbotfactory.com";
        $bannedReason = "Is a nogoodnik from a shady Russian bot factory";
        $this->userService->banEmail($bannedEmail, $bannedReason);
        $this->assertTrue($this->userService->emailIsBanned($bannedEmail));
        $hasBanned = false;
        foreach ($this->userService->getBannedEmails() as $bannedEmailAddress) {
            if ($bannedEmailAddress->getBannedEmailAddress() == $bannedEmail && $bannedEmailAddress->getBanReason() == $bannedReason) {
                $hasBanned = true;
            }
        }
        $this->assertTrue($hasBanned);
    }
}