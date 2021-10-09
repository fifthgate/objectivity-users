<?php

namespace Fifthgate\Objectivity\Users\Infrastructure\Repository;

use Fifthgate\Objectivity\Users\Infrastructure\Repository\Interfaces\UserRepositoryInterface;
use Fifthgate\Objectivity\Users\Infrastructure\Mapper\Interfaces\UserMapperInterface;
use Fifthgate\Objectivity\Users\Infrastructure\Mapper\Interfaces\BannedEmailsMapperInterface;
use Fifthgate\Objectivity\Users\Domain\Interfaces\UserInterface;
use Fifthgate\Objectivity\Users\Domain\Collection\Interfaces\UserCollectionInterface;
use Fifthgate\Objectivity\Repositories\Infrastructure\Repository\AbstractDomainEntityRepository;
use Fifthgate\Objectivity\Users\Domain\Collection\Interfaces\BannedEmailCollectionInterface;
use Fifthgate\Objectivity\Users\Domain\BannedEmailAddress;

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

    public function getBanReason(string $emailAddress) : ? string
    {
        $result = $this->bannedEmailsMapper->queryOne([
            'email' => $emailAddress
        ]);

        return $result ? $result->getBanReason() : null;
    }
}
