<?php

namespace Fifthgate\Objectivity\Users\Domain\Collection;

use Fifthgate\Objectivity\Core\Domain\Collection\AbstractDomainEntityCollection;
use Fifthgate\Objectivity\Users\Domain\Collection\Interfaces\UserRoleCollectionInterface;
use Fifthgate\Objectivity\Users\Domain\ValueObjects\Interfaces\UserRoleInterface;
use Fifthgate\Objectivity\Core\Domain\Collection\Traits\JsonSerializesCollection;

class UserRoleCollection extends AbstractDomainEntityCollection implements UserRoleCollectionInterface
{
    use JsonSerializesCollection;

    private $permissionsCache = [];

    public function getRoleByName(string $roleName): ?UserRoleInterface
    {
        foreach ($this->collection as $item) {
            if ($item->getMachineName() == $roleName) {
                return $item;
            }
        }
        return null;
    }

    public function getAllPermissions(): array
    {
        // @codeCoverageIgnoreStart
        if (!empty($this->permissionsCache)) {
            return $this->permissionsCache;
        }
        // @codeCoverageIgnoreEnd
        $permissions = [];
        foreach ($this->collection as $item) {
            foreach ($item->getPermissions() as $permissionName) {
                if ($permissionName != '*' && !isset($permissions[$permissionName])) {
                    $permissions[$permissionName] = $permissionName;
                }
            }
        }
        $this->permissionsCache = $permissions;
        return $permissions;
    }

    public function filterByMachineNames(array $machineNames): ?UserRoleCollectionInterface
    {
        $collection = new self();
        foreach ($this->collection as $role) {
            if (in_array($role->getMachineName(), $machineNames)) {
                $collection->add($role);
            }
        }
        return $collection;
    }
}
