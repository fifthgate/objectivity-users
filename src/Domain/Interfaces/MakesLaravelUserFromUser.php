<?php

namespace Fifthgate\Objectivity\Users\Domain\Interfaces;

use Fifthgate\Objectivity\Users\Domain\Interfaces\LaravelUserInterface;
use Fifthgate\Objectivity\Users\Domain\Interfaces\UserInterface;

interface MakesLaravelUserFromUser
{
    public static function makeFromUser(UserInterface $user) : LaravelUserInterface;
}
