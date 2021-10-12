<?php

namespace Fifthgate\Objectivity\Users\Domain\Collection;

use Fifthgate\Objectivity\Core\Domain\Collection\AbstractDomainEntityCollection;
use Fifthgate\Objectivity\Users\Domain\Collection\Interfaces\UserCollectionInterface;
use Fifthgate\Objectivity\Core\Domain\Collection\Traits\JsonSerializesCollection;

class UserCollection extends AbstractDomainEntityCollection implements UserCollectionInterface
{
	use JsonSerializesCollection;
}
