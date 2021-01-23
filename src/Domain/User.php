<?php

namespace Fifthgate\Objectivity\Users\Domain;

use Fifthgate\Objectivity\Users\Domain\Interfaces\UserInterface;
use \DateTimeInterface;
use Fifthgate\Objectivity\Users\Domain\Collection\Interfaces\UserRoleCollectionInterface;
use Fifthgate\Objectivity\Core\Domain\AbstractDomainEntity;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;

class User extends AbstractDomainEntity implements UserInterface
{
    use Notifiable;

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
     * Laravel's password reset system expects to be able to access some properties as though they were public. Le sigh.
     */
    public function __get(string $name)
    {
        switch ($name) {
            case 'name':
                return $this->getName();
                break;
            case 'email':
                return $this->getEmailAddress();
                break;
            case 'password':
                return $this->getPassword();
                break;
                break;
            default:
                return null;
        }
    }
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

    public function getEmailForPasswordReset()
    {
        return $this->getEmailAddress();
    }

    /**
     * @codeCoverageIgnore
     *
     * This is a straight carbon-copy of Laravel's system, for compatibility only.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function getEmailOptIn() : bool
    {
        return $this->emailOptIn;
    }

    public function setEmailOptIn(bool $emailOptIn)
    {
        $this->emailOptIn = $emailOptIn;
    }


    public function getAPIToken() : string
    {
        return $this->apiToken;
    }

    public function setAPIToken(string $apiToken)
    {
        $this->apiToken = $apiToken;
    }
}
