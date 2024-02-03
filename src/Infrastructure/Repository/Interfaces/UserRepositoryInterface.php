<?php

namespace Fifthgate\Objectivity\Users\Infrastructure\Repository\Interfaces;

use Fifthgate\Objectivity\Users\Domain\Interfaces\UserInterface;
use Fifthgate\Objectivity\Users\Domain\Collection\Interfaces\UserCollectionInterface;
use Fifthgate\Objectivity\Repositories\Infrastructure\Repository\Interfaces\DomainEntityRepositoryInterface;
use Fifthgate\Objectivity\Users\Domain\Collection\Interfaces\BannedEmailCollectionInterface;

interface UserRepositoryInterface extends DomainEntityRepositoryInterface
{
    public function retrieveByCredentials(array $credentials): ?UserInterface;

    public function retrieveByIDAndToken(int $id, string $token): ?UserInterface;

    public function banEmail(string $emailAddress, string $reason);

    public function getBannedEmails(): BannedEmailCollectionInterface;

    public function emailIsBanned(string $emailAddress): bool;

    public function getBanReason(string $emailAddress): ?string;
}
