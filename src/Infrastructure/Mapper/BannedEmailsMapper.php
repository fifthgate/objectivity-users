<?php

namespace Fifthgate\Objectivity\Users\Infrastructure\Mapper;

use Fifthgate\Objectivity\Users\Infrastructure\Mapper\Interfaces\BannedEmailsMapperInterface;
use Illuminate\Database\DatabaseManager as DB;
use Fifthgate\Objectivity\Users\Domain\BannedEmailAddress;
use Carbon\Carbon;
use Fifthgate\Objectivity\Users\Domain\Collection\Interfaces\BannedEmailCollectionInterface;
use Fifthgate\Objectivity\Users\Domain\Collection\BannedEmailsCollection;
use Fifthgate\Objectivity\Repositories\Infrastructure\Mapper\AbstractDomainEntityMapper;
use Fifthgate\Objectivity\Core\Domain\Collection\Interfaces\DomainEntityCollectionInterface;
use Fifthgate\Objectivity\Core\Domain\Interfaces\DomainEntityInterface;
use \Exception;

class BannedEmailsMapper extends AbstractDomainEntityMapper implements BannedEmailsMapperInterface
{
    protected string $tableName = 'banned_emails';

    protected bool $publishes = false;

    protected bool $softDeletes = false;


    public function makeCollection() : DomainEntityCollectionInterface
    {
        return new BannedEmailsCollection;
    }

    public function mapEntity(array $result) : DomainEntityInterface
    {
        return self::staticMap($result);
    }


    public static function staticMap(array $result) : DomainEntityInterface
    {
        $bannedEmail = $result['id'] ? new BannedEmailAddress($result['email'], $result['ban_reason'], $result['id']) : new BannedEmailAddress($result['email'], $result['ban_reason']);

        $bannedEmail->setUpdatedAt(new Carbon($result['updated_at']));
        $bannedEmail->setCreatedAt(new Carbon($result['created_at']));
        return $bannedEmail;
    }

    /**
     * @codeCoverageIgnore
     *
     * At time of writing, this cannot be called by the main system.
     *
     * @param  DomainEntityInterface $domainEntity The Banned Email entity
     * @return DomainEntityInterface The Banned Email Entity
     */
    protected function update(DomainEntityInterface $domainEntity) : DomainEntityInterface
    {
        throw new Exception("You can't update a banned e-mail");
    }

    protected function create(DomainEntityInterface $domainEntity) : DomainEntityInterface
    {
        $createdAt = new Carbon;
        $id = $this->db->table($this->getTableName())->insertGetId([
            'email' => $domainEntity->getBannedEmailAddress(),
            'ban_reason' => $domainEntity->getBanReason(),
            'created_at' => $createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $createdAt->format('Y-m-d H:i:s'),
        ]);
        $domainEntity->setID($id);
        return $domainEntity;
    }
}
