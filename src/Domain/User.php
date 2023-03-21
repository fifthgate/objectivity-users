<?php

namespace Fifthgate\Objectivity\Users\Domain;

use Fifthgate\Objectivity\Users\Domain\Interfaces\UserInterface;
use \DateTimeInterface;
use Fifthgate\Objectivity\Users\Domain\Collection\Interfaces\UserRoleCollectionInterface;
use Fifthgate\Objectivity\Core\Domain\AbstractSerializableDomainEntity;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Notifications\Notifiable;

class User extends AbstractSerializableDomainEntity implements UserInterface
{

    protected string $password;

    protected ?string $rememberToken = null;

    protected string $name;

    protected ? UserRoleCollectionInterface $roles = null;

    protected string $emailAddress;

    protected string $apiToken;

    protected bool $isActivated = false;

    protected bool $cookieAcceptanceStatus = false;

    protected string $optOutToken;

    protected bool $emailOptIn = false;

    protected bool $banned = false;

    public function __construct(
        string $emailAddress,
        string $name,
        bool $isActivated,
        bool $cookieAcceptanceStatus,
        bool $emailOptIn,
        bool $banned
    ) {
        $this->emailAddress = $emailAddress;
        $this->name = $name;
        $this->isActivated = $isActivated;
        $this->cookieAcceptanceStatus = $cookieAcceptanceStatus;
        $this->emailOptIn = $emailOptIn;
        $this->banned = $banned;
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier(): int
    {
        return $this->getID();
    }

    /**
     * @param string $password
     * @return void
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string|null
     */
    public function getPassword() : ? string
    {
        return $this->password;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string $emailAddress
     * @return void
     */
    public function setEmailAddress(string $emailAddress): void
    {
        $this->emailAddress = $emailAddress;
    }

    /**
     * @return string
     */
    public function getEmailAddress() : string
    {
        return $this->emailAddress;
    }


    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken(): ? string
    {
        return $this->rememberToken;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value): void
    {
        $this->rememberToken = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword(): string
    {
        return $this->getPassword();
    }

    public function hasRole(string $roleName) : bool
    {
        if (isset($this->roles)) {
            return $this->roles->getRoleByName($roleName) !== null;
        }
        return false;
    }

    public function setRoles(UserRoleCollectionInterface $roles): void
    {
        $this->roles = $roles;
    }

    public function getRoles() : ? UserRoleCollectionInterface
    {
        return $this->roles;
    }

    public function hasPermission(string $permissionName) : bool
    {
        if ($this->roles) {
            foreach ($this->roles as $role) {
                if ($role->hasPermission($permissionName)) {
                    return true;
                }
            }
        }
        
        return false;
    }

    public function setIsActivated(bool $isActivated): void
    {
        $this->isActivated = $isActivated;
    }

    public function getIsActivated() : bool
    {
        return $this->isActivated;
    }

    public function setCookieAcceptanceStatus(bool $cookieAcceptanceStatus): void
    {
        $this->cookieAcceptanceStatus = $cookieAcceptanceStatus;
    }

    public function getCookieAcceptanceStatus() : bool
    {
        return $this->cookieAcceptanceStatus;
    }

    public function setOptOutToken(string $optOutToken): void
    {
        $this->optOutToken = $optOutToken;
    }

    public function getOptOutToken() : ? string
    {
        return isset($this->optOutToken) ? $this->optOutToken : null;
    }

    public function getEmailOptIn() : bool
    {
        return $this->emailOptIn;
    }

    public function setEmailOptIn(bool $emailOptIn): void
    {
        $this->emailOptIn = $emailOptIn;
    }


    public function getAPIToken() : ? string
    {
        return isset($this->apiToken) ? $this->apiToken : null;
    }

    public function setAPIToken(string $apiToken)
    {
        $this->apiToken = $apiToken;
    }

    /**
     * Serialize the object to an array.
     *
     * @return array An array of object variables, based on get methods.
     */
    public function jsonSerialize($excludedMethods = []): array
    {
        //This would only get the cryptographic hash of the password, but we're still better not to expose it, even over a secured connection.
        $securityExcludedMethods = [
            "getPassword",
            "getRememberToken",
            "getAuthPassword",
            "getAuthIdentifierName",
            "getAuthIdentifier"
        ];
        foreach ($securityExcludedMethods as $securityExcludedMethod) {
            if (!in_array($securityExcludedMethods, $excludedMethods)) {
                $excludedMethods[] = $securityExcludedMethod;
            }
        }
        
        return parent::jsonSerialize($excludedMethods);
    }


    public function setBanned(bool $banned): void
    {
        $this->banned = $banned;
    }

    public function isBanned() : bool
    {
        return $this->banned;
    }
}
