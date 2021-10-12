<?php

namespace Fifthgate\Objectivity\Users\Service;

use Illuminate\Contracts\Auth\Authenticatable;
use Fifthgate\Objectivity\Users\Service\Interfaces\UserServiceInterface;
use Fifthgate\Objectivity\Users\Infrastructure\Repository\Interfaces\UserRepositoryInterface;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Fifthgate\Objectivity\Users\Domain\Collection\Interfaces\UserRoleCollectionInterface;
use Fifthgate\Objectivity\Users\Domain\Collection\Interfaces\UserCollectionInterface;
use Fifthgate\Objectivity\Users\Domain\Interfaces\UserInterface;
use Fifthgate\Objectivity\Repositories\Service\AbstractRepositoryDrivenDomainEntityManagementService;
use Illuminate\Support\Str;
use Fifthgate\Objectivity\Users\Domain\Collection\Interfaces\BannedEmailCollectionInterface;
use Fifthgate\Objectivity\Users\Domain\Interfaces\LaravelUserInterface;
use Fifthgate\Objectivity\Core\Domain\Interfaces\DomainEntityInterface;
use Fifthgate\Objectivity\Users\Domain\LaravelUser;

class UserService extends AbstractRepositoryDrivenDomainEntityManagementService implements UserServiceInterface
{
    protected $userRepository;

    protected $hasher;

    protected $roles;

    public function __construct(
        UserRepositoryInterface $userRepository,
        HasherContract $hasher,
        UserRoleCollectionInterface $roles
    ) {
        $this->hasher = $hasher;
        $this->roles = $roles;
        parent::__construct($userRepository);
    }

    public function getEntityInfo() : array
    {
        return [
            'user' => [
                'name' => 'User',
                'softDeletes' => false,
                'publishes' => false,
                'timestamps' => true
            ]
        ];
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        return $this->repository->find($identifier);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        return $this->repository->retrieveByIDAndToken($identifier, $token);
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);
        $this->save($user);
    }

    public function save(DomainEntityInterface $domainEntity) : DomainEntityInterface
    {
        if (!$domainEntity->getOptOutToken()) {
            $domainEntity->setOptOutToken($this->generateOptOutToken());
        }
        return $this->repository->save($domainEntity);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) ||
           (count($credentials) === 1 &&
            array_key_exists('password', $credentials))) {
            return;
        }
        return $this->repository->retrieveByCredentials($credentials);
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return $this->getHasher()->check(
            $credentials['password'],
            $user->getAuthPassword()
        );
    }

    public function getRoles() : ? UserRoleCollectionInterface
    {
        return $this->roles;
    }

    public function getHasher() : HasherContract
    {
        return $this->hasher;
    }

    public function hashPassword(string $password) : string
    {
        return $this->getHasher()->make($password);
    }

    public function generateRandomPassword(int $length) : string
    {
        return Str::random($length);
    }

    public function banEmail(string $emailAddress, string $reason)
    {
        $user = $this->retrieveByCredentials(["email" => $emailAddress]);
        if ($user) {
            $user->setBanned(true);
            $this->save($user);
        }
        return $this->repository->banEmail($emailAddress, $reason);
    }

    public function getBannedEmails() : BannedEmailCollectionInterface
    {
        return $this->repository->getBannedEmails();
    }

    public function emailIsBanned(string $emailAddress) : bool
    {
        return $this->repository->emailIsBanned($emailAddress);
    }

    public function getBanReason(string $emailAddress) : ? string
    {
        return $this->repository->getBanReason($emailAddress);
    }

    public function generateOptOutToken(): string
    {
        return Str::uuid();
    }

    public function generateLaravelCompatibleUser(UserInterface $user) : LaravelUserInterface
    {
        return LaravelUser::makeFromUser($user);
    }
}
