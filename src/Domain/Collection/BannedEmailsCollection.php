<?php

namespace Fifthgate\Objectivity\Users\Domain\Collection;

use Fifthgate\Objectivity\Core\Domain\Collection\AbstractDomainEntityCollection;
use Fifthgate\Objectivity\Users\Domain\Collection\Interfaces\BannedEmailCollectionInterface;
use Fifthgate\Objectivity\Core\Domain\Collection\Traits\JsonSerializesCollection;

class BannedEmailsCollection extends AbstractDomainEntityCollection implements BannedEmailCollectionInterface
{
    use JsonSerializesCollection;
}
