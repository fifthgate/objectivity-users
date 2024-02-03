<?php

namespace Fifthgate\Objectivity\Users\Domain\Interfaces;

use Fifthgate\Objectivity\Core\Domain\Interfaces\DomainEntityInterface;

interface BannedEmailInterface extends DomainEntityInterface
{
    public function getBannedEmailAddress(): string;

    public function getBanReason(): string;
}
