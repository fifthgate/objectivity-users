<?php

namespace Fifthgate\Objectivity\Users\Domain\ValueObjects\Interfaces;

use Fifthgate\Objectivity\Core\Domain\Interfaces\DomainEntityInterface;

interface UserRoleInterface extends DomainEntityInterface
{
    public function getMachineName(): string;

    public function getName(): string;

    public function getDescription();

    public function getPermissions(): array;

    public function hasPermission(string $permissionName): bool;
}
