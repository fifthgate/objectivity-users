<?php

namespace Fifthgate\Objectivity\Users\Domain\ValueObjects;

use Fifthgate\Objectivity\Users\Domain\ValueObjects\Interfaces\UserRoleInterface;
use Fifthgate\Objectivity\Core\Domain\AbstractDomainEntity;

class UserRole extends AbstractDomainEntity implements UserRoleInterface
{
    private string $machineName;

    private string $name;

    private string $description;

    private array $permissions;

    public function __construct(
        string $machineName,
        string $name,
        string $description,
        array $permissions
    ) {
        $this->machineName = $machineName;
        $this->name = $name;
        $this->description = $description;
        $this->permissions = $permissions;
    }

    public function getID() : int
    {
        return 0;
    }
    public function getMachineName() : string
    {
        return $this->machineName;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getPermissions() : array
    {
        return $this->permissions;
    }

    public function hasPermission(string $permissionName) : bool
    {
        foreach ($this->permissions as $permission) {
            if ($permission == '*') {
                return true;
            }
            if ($permission == $permissionName) {
                return true;
            }
        }
        return false;
    }
}
