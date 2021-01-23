<?php

namespace Fifthgate\Objectivity\Users\Domain;

use Fifthgate\Objectivity\Users\Domain\Interfaces\BannedEmailInterface;
use \DateTimeInterface;
use Fifthgate\Objectivity\Core\Domain\AbstractTimestampingDomainEntity;

class BannedEmailAddress extends AbstractTimestampingDomainEntity implements BannedEmailInterface
{

    protected string $bannedEmailAddress;

    protected string $banReason;

    public function __construct(
        string $bannedEmailAddress,
        string $banReason,
        int $id = null
    ) {
        $this->bannedEmailAddress = $bannedEmailAddress;
        $this->banReason = $banReason;
        if ($id) {
            $this->setID($id);
        }
    }

    public function getBannedEmailAddress() : string
    {
        return $this->bannedEmailAddress;
    }

    public function getBanReason() : string
    {
        return $this->banReason;
    }
}
