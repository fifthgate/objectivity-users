<?php

namespace Fifthgate\Objectivity\Users\Domain\Interfaces;

use Illuminate\Contracts\Auth\Authenticatable;
use Fifthgate\Objectivity\Users\Domain\Collection\Interfaces\UserRoleCollectionInterface;
use \DateTimeInterface;
use Fifthgate\Objectivity\Core\Domain\Interfaces\DomainEntityInterface;


interface UserInterface extends Authenticatable, DomainEntityInterface
{
    public function hasRole(string $roleName) : bool;

    public function setRoles(UserRoleCollectionInterface $roles);

    public function getRoles() : ? UserRoleCollectionInterface;

    public function hasPermission(string $permissionName) : bool;

    public function setID($id);

    public function getID();

    public function setPassword(string $password);

    public function getPassword() : ? string;

    public function setName(string $name);

    public function getName() : string;

    public function setEmailAddress(string $emailAddress);

    public function getEmailAddress() : string;

    public function getCreatedAt() : DateTimeInterface;

    public function setUpdatedAt(DateTimeInterface $updatedAt);

    public function getUpdatedAt() : DateTimeInterface;

    public function setIsActivated(bool $isActivated);

    public function getIsActivated() : bool;

    public function setCookieAcceptanceStatus(bool $cookieAcceptanceStatus);

    public function getCookieAcceptanceStatus() : bool;

    public function setOptOutToken(string $optOutToken);

    public function getOptOutToken() : ? string;

    public function getEmailOptIn() : bool;

    public function setEmailOptIn(bool $emailOptIn);

    public function getAPIToken() : string;

    public function setAPIToken(string $apiToken);
}
