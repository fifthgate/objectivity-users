<?php

namespace Fifthgate\Objectivity\Users\Domain\Collection\Interfaces;

use Fifthgate\Objectivity\Core\Domain\Collection\Interfaces\DomainEntityCollectionInterface;
use Fifthgate\Objectivity\Users\Domain\ValueObjects\Interfaces\UserRoleInterface;

interface UserRoleCollectionInterface extends DomainEntityCollectionInterface
{
    public function getRoleByName(string $roleName) : ? UserRoleInterface;

    public function getAllPermissions() : array;

    public function filterByMachineNames(array $machineNames) : ? UserRoleCollectionInterface;
}
