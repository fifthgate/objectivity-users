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

    protected $password;

    protected $rememberToken;

    protected $name;

    protected $roles;

    protected $emailAddress;

    protected string $apiToken;

    protected bool $isActivated = false;

    protected bool $cookieAcceptanceStatus = false;

    protected string $optOutToken;

    protected bool $emailOptIn = false;

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getID();
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    public function getPassword() : ? string
    {
        return $this->password;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setEmailAddress(string $emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }

    public function getEmailAddress() : string
    {
        return $this->emailAddress;
    }


    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->rememberToken;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        $this->rememberToken = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->getPassword();
    }

    public function hasRole(string $roleName) : bool
    {
        if ($this->roles) {
            return $this->roles->getRoleByName($roleName) != null;
        }
        return false;
    }

    public function setRoles(UserRoleCollectionInterface $roles)
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

    public function setIsActivated(bool $isActivated)
    {
        $this->isActivated = $isActivated;
    }

    public function getIsActivated() : bool
    {
        return $this->isActivated;
    }

    public function setCookieAcceptanceStatus(bool $cookieAcceptanceStatus)
    {
        $this->cookieAcceptanceStatus = $cookieAcceptanceStatus;
    }

    public function getCookieAcceptanceStatus() : bool
    {
        return $this->cookieAcceptanceStatus;
    }

    public function setOptOutToken(string $optOutToken)
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

    public function setEmailOptIn(bool $emailOptIn)
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
    public function jsonSerialize($excludedMethods = [])
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
}
