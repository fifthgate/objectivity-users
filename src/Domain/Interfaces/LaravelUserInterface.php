<?php

namespace Fifthgate\Objectivity\Users\Domain\Interfaces;

use Illuminate\Contracts\Auth\CanResetPassword;
use Fifthgate\Objectivity\Users\Domain\Interfaces\UserInterface;

interface LaravelUserInterface extends UserInterface, CanResetPassword {

}