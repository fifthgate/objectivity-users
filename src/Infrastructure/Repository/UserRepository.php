<?php

namespace Services\User\Infrastructure\Repository;

use Services\User\Infrastructure\Repository\Interfaces\UserRepositoryInterface;
use Services\User\Infrastructure\Mapper\Interfaces\UserMapperInterface;
use Services\User\Infrastructure\Mapper\Interfaces\BannedEmailsMapperInterface;
use Services\User\Domain\Interfaces\UserInterface;
use Services\User\Domain\Collection\Interfaces\UserCollectionInterface;
use Services\Core\Infrastructure\Repository\AbstractDomainEntityRepository;
use Services\User\Domain\Collection\Interfaces\BannedEmailCollectionInterface;
use Services\User\Domain\BannedEmailAddress;

class UserRepository extends AbstractDomainEntityRepository implements UserRepositoryInterface
{
    protected $mapper;

    protected $bannedEmailsMapper;

    public function __construct(
        UserMapperInterface $mapper,
        BannedEmailsMapperInterface $bannedEmailsMapper
    ) {
        $this->mapper = $mapper;
        $this->bannedEmailsMapper = $bannedEmailsMapper;
    }

    public function retrieveByCredentials(array $credentials) : ? UserInterface
    {
        return $this->mapper->retrieveByCredentials($credentials);
    }

    public function retrieveByIDAndToken(int $id, string $token) : ? UserInterface
    {
        return $this->mapper->queryOne([
            'id' => $id,
            'remember_token' => $token
        ]);
    }

    public function banEmail(string $emailAddress, string $reason)
    {
        $bannedEmail = new BannedEmailAddress($emailAddress, $reason);
        return $this->bannedEmailsMapper->save($bannedEmail);
    }

    public function getBannedEmails() : BannedEmailCollectionInterface
    {
        return $this->bannedEmailsMapper->findAll();
    }

    public function emailIsBanned(string $emailAddress) : bool
    {
        $result = $this->bannedEmailsMapper->queryOne([
            'email' => $emailAddress
        ]);
        return (bool) $result;
    }
}
