<?php

namespace Fifthgate\Objectivity\Users\Domain;

use Fifthgate\Objectivity\Users\Domain\User;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Fifthgate\Objectivity\Users\Domain\Interfaces\LaravelUserInterface;
use Fifthgate\Objectivity\Users\Domain\Interfaces\UserInterface;
use Fifthgate\Objectivity\Users\Domain\Interfaces\MakesLaravelUserFromUser;

/**
 * Compatibility Decorator for User
 */
class LaravelUser extends User implements LaravelUserInterface, MakesLaravelUserFromUser
{
    use Notifiable;

    /**
     * @codeCoverageIgnore
     *
     * This is a straight carbon-copy of Laravel's system, for compatibility only.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }


    public function getEmailForPasswordReset()
    {
        return $this->getEmailAddress();
    }

    /**
     * Laravel's password reset system expects to be able to read some properties as though they were public.
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
                
            default:
                return null;
        }
    }

    public static function makeFromUser(UserInterface $user) : LaravelUserInterface
    {
        $laravelUser = new self;
        if ($user->getID()) {
            $laravelUser->setID($user->getID());
        }
        $laravelUser->setName($user->getName());
        $laravelUser->setPassword($user->getPassword());
        $laravelUser->setName($user->getName());
        $laravelUser->setEmailAddress($user->getEmailAddress());
        return $laravelUser;
    }
}
