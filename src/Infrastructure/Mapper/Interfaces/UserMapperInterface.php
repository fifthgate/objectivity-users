<?php

namespace Fifthgate\Objectivity\Users\Infrastructure\Mapper\Interfaces;

use Fifthgate\Objectivity\Users\Domain\Interfaces\UserInterface;
use Fifthgate\Objectivity\Users\Domain\Collection\Interfaces\UserRoleCollectionInterface;
use Fifthgate\Objectivity\Users\Domain\Collection\Interfaces\UserCollectionInterface;

interface UserMapperInterface
{
    public function retrieveByCredentials(array $credentials): ?UserInterface;

    public function getRolesForUserID(int $userID): ?UserRoleCollectionInterface;
}
