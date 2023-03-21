<?php

namespace Fifthgate\Objectivity\Users\Tests\Functional;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use Fifthgate\Objectivity\Users\Service\Interfaces\UserServiceInterface;
use Fifthgate\Objectivity\Users\Domain\User;
use \DateTime;
use Illuminate\Support\Facades\Hash;
use \DateTimeInterface;
use Fifthgate\Objectivity\Users\Domain\Collection\Interfaces\UserCollectionInterface;
use Fifthgate\Objectivity\Users\Domain\Interfaces\UserInterface;
use Fifthgate\Objectivity\Users\Tests\ObjectivityUsersTestCase;
use Fifthgate\Objectivity\Users\Domain\Interfaces\LaravelUserInterface;

class UserServiceTest extends ObjectivityUsersTestCase
{
    public function testUserStores()
    {
        $app = $this->createApplication();
        $user = $this->generateTestUser();
        $user = $this->userService->save($user);
        $this->assertDatabaseHas('users', [
            'email' => 'lipsum@lauraipsum.com',
            'name' => 'Laura Ipsum',
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
        $testStart = new DateTime;
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
        $testStart = new DateTime;
        $user = $this->generateTestUser();
        $user = $this->userService->save($user);
        $this->userService->updateRememberToken($user, 'rememberToken');
        $user = $this->userService->retrieveById($user->getID());
        $this->assertEquals($user->getRememberToken(), 'rememberToken');
    }

    public function testRetrieveByToken()
    {
        $testStart = new DateTime;
        $user = $this->generateTestUser();
        $user = $this->userService->save($user);
        $id = $user->getID();
        $this->userService->updateRememberToken($user, 'rememberToken');
        $user = $this->userService->retrieveByToken($user->getID(), 'rememberToken');
        $this->assertEquals($user->getID(), $id);
    }

    public function testValidateCredentials()
    {
        $testStart = new DateTime;
        $user = $this->generateTestUser();
        $user = $this->userService->save($user);
        $this->assertTrue($this->userService->validateCredentials($user, ['password'=>'LoremIpsum']));
    }

    public function testFindAll()
    {
        $testStart = new DateTime;
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
        $testStart = new DateTime;
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
        $createdAt = new DateTime;
        $testRoles = $this->userService->getRoles()->filterByMachineNames(["registered-user"]);
        $user = new User(
            $testEmailAddress,
            $testName,
            true,
            false,
            false,
            false
        );

        $user->setPassword($testPassword);
        $user->setRoles($testRoles);
        $user->setCreatedAt($createdAt);
        $user->setUpdatedAt($createdAt);
        $user->setAPIToken('LoremIpsum');
        $optOutToken = $this->userService->generateOptOutToken();
        $user->setOptOutToken($optOutToken);
        $user->setBanned(true);
        $user = $this->userService->save($user);
        $foundUser = $this->userService->find($user->getID());

        //Test failure condition.
        $this->assertEquals($optOutToken, $foundUser->getOptOutToken());
        $this->assertEquals("LoremIpsum", $foundUser->getAPIToken());
        $this->assertEquals($testPassword, $foundUser->getPassword());
        $this->assertEquals($testRoles->count(), $foundUser->getRoles()->count());
        $this->assertEquals($testEmailAddress, $foundUser->getEmailAddress());
        $this->assertEquals($testName, $foundUser->getName());
        $this->assertFalse($foundUser->getCookieAcceptanceStatus());
        $this->assertTrue($foundUser->getIsActivated());
        $this->assertTrue($foundUser->isBanned());
    }

    public function testLackOfOptOutToken()
    {
        $testPassword = "Test";
        $testName = "Test Name";
        $testEmailAddress = "probity@inaction.gov";
        $createdAt = new DateTime;
        $testRoles = $this->userService->getRoles()->filterByMachineNames(["registered-user"]);
        $user = new User(
            $testEmailAddress,
            $testName,
            true,
            false,
            false,
            false,
        );
        $user->setPassword($testPassword);
        $user->setName($testName);
        $user->setEmailAddress($testEmailAddress);
        $user->setRoles($testRoles);
        $user->setIsActivated(true);
        $user->setCookieAcceptanceStatus(false);
        $user->setCreatedAt($createdAt);
        $user->setUpdatedAt($createdAt);
        $user->setAPIToken('LoremIpsum');
        
        $user = $this->userService->save($user);
        $foundUser = $this->userService->find($user->getID());
        
        $this->assertNotNull($foundUser->getOptOutToken());
    }


    public function testBanSystem()
    {
        $bannedEmail = "nogoodnik@shadyrussianbotfactory.com";
        $bannedReason = "Is a nogoodnik from a shady Russian bot factory";

        $this->userService->save($this->generateTestUser(["email" => $bannedEmail]));

        $this->assertNull($this->userService->getBanReason($bannedEmail));
        $this->userService->banEmail($bannedEmail, $bannedReason);
        $this->assertTrue($this->userService->emailIsBanned($bannedEmail));
        $hasBanned = false;
        foreach ($this->userService->getBannedEmails() as $bannedEmailAddress) {
            if ($bannedEmailAddress->getBannedEmailAddress() == $bannedEmail && $bannedEmailAddress->getBanReason() == $bannedReason) {
                $hasBanned = true;
            }
        }
        $this->assertTrue($hasBanned);
        $foundUser = $this->userService->retrieveByCredentials(["email" => $bannedEmail]);
        $this->assertTrue($foundUser->isBanned());
        $this->assertEquals($bannedReason, $this->userService->getBanReason($bannedEmail));
    }

    public function testGenerateLaravelCompatibleUser()
    {
        $testPassword = "Test";
        $testName = "Test Name";
        $testEmailAddress = "probity@inaction.gov";
        $createdAt = new DateTime;
        $testRoles = $this->userService->getRoles()->filterByMachineNames(["registered-user"]);
        $user = new User(
            $testEmailAddress,
            $testName,
            true,
            false,
            false,
            false,
        );
        $user->setPassword($testPassword);
        $user->setName($testName);
        $user->setEmailAddress($testEmailAddress);
        $user->setRoles($testRoles);
        $user->setIsActivated(true);
        $user->setCookieAcceptanceStatus(false);
        $user->setCreatedAt($createdAt);
        $user->setUpdatedAt($createdAt);
        $user->setAPIToken('LoremIpsum');
        $optOutToken = $this->userService->generateOptOutToken();
        $user->setOptOutToken($optOutToken);
        $user = $this->userService->save($user);

        $laravelUser = $this->userService->generateLaravelCompatibleUser($user);
        $this->assertTrue($laravelUser instanceof LaravelUserInterface);
        $this->assertEquals($user->getID(), $laravelUser->getID());
        $this->assertEquals($user->getPassword(), $laravelUser->getPassword());
        $this->assertEquals($user->getEmailAddress(), $laravelUser->getEmailAddress());

        $this->assertEquals($user->getEmailAddress(), $laravelUser->getEmailForPasswordReset());
        $this->assertEquals($user->getName(), $laravelUser->name);
        $this->assertEquals($user->getEmailAddress(), $laravelUser->email);
        $this->assertEquals($user->getPassword(), $laravelUser->password);
        $this->assertNull($laravelUser->someotherpropertythatdoesntexist);
        $this->assertEquals($user->getCreatedAt(), $laravelUser->getCreatedAt());
        $this->assertEquals($user->getUpdatedAt(), $laravelUser->getUpdatedAt());
    }
}
