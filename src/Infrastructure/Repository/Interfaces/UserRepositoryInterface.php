<?php

namespace Services\User\Infrastructure\Repository\Interfaces;

use Services\User\Domain\Interfaces\UserInterface;
use Services\User\Domain\Collection\Interfaces\UserCollectionInterface;
use Services\Core\Infrastructure\Repository\Interfaces\DomainEntityRepositoryInterface;
use Services\User\Domain\Collection\Interfaces\BannedEmailCollectionInterface;

interface UserRepositoryInterface extends DomainEntityRepositoryInterface
{
    public function retrieveByCredentials(array $credentials) : ? UserInterface;

    public function retrieveByIDAndToken(int $id, string $token) : ? UserInterface;

    public function banEmail(string $emailAddress, string $reason);

    public function getBannedEmails() : BannedEmailCollectionInterface;

    public function emailIsBanned(string $emailAddress) : bool;
}
