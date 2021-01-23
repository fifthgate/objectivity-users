<?php

namespace Fifthgate\Objectivity\Users\Service\Interfaces;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Fifthgate\Objectivity\Users\Domain\Collection\Interfaces\UserCollectionInterface;
use Fifthgate\Objectivity\Users\Domain\Interfaces\UserInterface;
use Fifthgate\Objectivity\Users\Domain\Collection\Interfaces\UserRoleCollectionInterface;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Fifthgate\Objectivity\Repositories\Service\Interfaces\DomainEntityManagementServiceInterface;
use Fifthgate\Objectivity\Users\Domain\Collection\Interfaces\BannedEmailCollectionInterface;

interface UserServiceInterface extends UserProvider, DomainEntityManagementServiceInterface
{
    public function getRoles() : ? UserRoleCollectionInterface;

    public function getHasher() : HasherContract;

    public function hashPassword(string $password) : string;

    public function generateRandomPassword(int $length) : string;

    public function banEmail(string $emailAddress, string $reason);

    public function getBannedEmails() : BannedEmailCollectionInterface;

    public function emailIsBanned(string $emailAddress) : bool;

    public function generateOptOutToken(): string;
}
