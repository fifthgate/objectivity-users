<?php

namespace Fifthgate\Objectivity\Users\Infrastructure\Mapper;

use Fifthgate\Objectivity\Users\Infrastructure\Mapper\Interfaces\UserMapperInterface;
use Illuminate\Database\DatabaseManager as DB;
use Fifthgate\Objectivity\Users\Domain\Interfaces\UserInterface;
use Fifthgate\Objectivity\Users\Domain\User;
use Carbon\Carbon;
use Fifthgate\Objectivity\Users\Domain\Collection\Interfaces\UserRoleCollectionInterface;
use Fifthgate\Objectivity\Users\Domain\Collection\UserRoleCollection;
use Fifthgate\Objectivity\Users\Domain\Collection\Interfaces\UserCollectionInterface;
use Fifthgate\Objectivity\Users\Domain\Collection\UserCollection;
use Fifthgate\Objectivity\Repositories\Infrastructure\Mapper\AbstractDomainEntityMapper;
use Fifthgate\Objectivity\Core\Domain\Collection\Interfaces\DomainEntityCollectionInterface;
use Fifthgate\Objectivity\Core\Domain\Interfaces\DomainEntityInterface;

class UserMapper extends AbstractDomainEntityMapper implements UserMapperInterface
{
    protected string $tableName = 'users';

    protected bool $publishes = false;

    protected bool $softDeletes = false;

    protected $roles;

    public function __construct(
        DB $db,
        UserRoleCollectionInterface $roles
    ) {
        parent::__construct($db);
        $this->roles = $roles;
    }

    public function makeCollection() : DomainEntityCollectionInterface
    {
        return new UserCollection;
    }

    /**
     * @codeCoverageIgnore
     */
    public static function staticMapMany(array $results) : DomainEntityCollectionInterface
    {
        $collection = new UserCollection;
        foreach ($results as $result) {
            $collection->add(self::staticMap((array) $result));
        }
        return $collection;
    }

    /**
     * @codeCoverageIgnore
     */
    public function retrieveByCredentials(array $credentials) : ? UserInterface
    {
        $query = $this->db->table($this->getTableName());
        foreach ($credentials as $credentialName => $credentialPayload) {
            if ($credentialName == 'password' || $credentialName == 'password_confirmation') {
                continue;
            }
            
            $query = $query->where($credentialName, '=', $credentialPayload);
        }
        
        $result = $query->first();
        
        return $result ? $this->mapEntity((array) $result) : null;
    }

    public function mapEntity(array $result) : DomainEntityInterface
    {
        $user = self::staticMap($result)       ;
        $roles = $this->getRolesForUserID($user->getID());
        if ($roles) {
            $user->setRoles($roles);
        }
        $user->hashSelf();
        return $user;
    }


    public static function staticMap(array $result) : DomainEntityInterface
    {
        $user = new User;
        if ($result['id']) {
            $user->setID($result['id']);
        }
        $user->setName($result['name']);
        $user->setPassword($result['password']);
        $user->setEmailAddress($result['email']);
        $user->setIsActivated((bool) $result['is_activated']);
        $user->setRememberToken($result['remember_token']);
        $user->setCookieAcceptanceStatus((bool) $result['has_cookie_consent']);
        $user->setCreatedAt(new Carbon($result['created_at']));
        $user->setUpdatedAt(new Carbon($result['updated_at']));
        if ($result['opt_out_token']) {
            $user->setOptOutToken($result['opt_out_token']);
        }
        $user->setEmailOptIn((bool) $result['email_opt_in']);
        $user->setAPIToken($result['api_token'] ?? '');
        return $user;
    }

    public function getRolesForUserID(int $userID) : ? UserRoleCollectionInterface
    {
        $results = $this->db->table('user_roles')->select('role_name')
            ->where('user_id', '=', $userID)
            ->get()
            ->toArray();
        return $results ? $this->mapRoles((array) $results) : null;
    }

    private function mapRoles(array $roleNames)
    {
        $collection = new UserRoleCollection;
        $hasRoles = false;
        foreach ($roleNames as $roleName) {
            $role = $this->roles->getRoleByName($roleName->role_name);
            if ($role) {
                $hasRoles = true;
                $collection->add($role);
            }
        }
        return $hasRoles ? $collection : null;
    }

    public function delete(DomainEntityInterface $user)
    {
        $this->purgeRolesForUser($user);
        parent::delete($user);
    }

    protected function update(DomainEntityInterface $domainEntity) : DomainEntityInterface
    {
        $this->purgeRolesForUser($domainEntity);
        $updatedAt = new Carbon;
        $this->db->table($this->getTableName())
            ->where('id', $domainEntity->getID())
            ->update([
                'name' => $domainEntity->getName(),
                'email' => $domainEntity->getEmailAddress(),
                'password' => $domainEntity->getPassword(),
                'remember_token' => $domainEntity->getRememberToken(),
                'created_at' => $domainEntity->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $updatedAt->format('Y-m-d H:i:s'),
                'is_activated' => $domainEntity->getIsActivated() ? 1 : 0,
                'has_cookie_consent' => $domainEntity->getCookieAcceptanceStatus() ? 1 : 0,
                'opt_out_token' => $domainEntity->getOptOutToken(),
                'email_opt_in' => (int) $domainEntity->getEmailOptIn(),
                'api_token' => $domainEntity->getAPIToken()
            ]);
        $this->insertRolesForUser($domainEntity);
        return $domainEntity;
    }

    protected function create(DomainEntityInterface $domainEntity) : DomainEntityInterface
    {
        $this->purgeRolesForUser($domainEntity);
        $updatedAt = new Carbon;
        $id = $this->db->table($this->getTableName())
            ->insertGetId([
                'name' => $domainEntity->getName(),
                'email' => $domainEntity->getEmailAddress(),
                'password' => $domainEntity->getPassword(),
                'remember_token' => $domainEntity->getRememberToken(),
                'created_at' => $domainEntity->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $updatedAt->format('Y-m-d H:i:s'),
                'is_activated' => $domainEntity->getIsActivated() ? 1 : 0,
                'has_cookie_consent' => $domainEntity->getCookieAcceptanceStatus() ? 1 : 0,
                'opt_out_token' => $domainEntity->getOptOutToken(),
                'email_opt_in' => (int) $domainEntity->getEmailOptIn(),
                'api_token' => $domainEntity->getAPIToken()
        ]);
        $domainEntity->setID($id);
        $this->insertRolesForUser($domainEntity);
        return $domainEntity;
    }

    protected function purgeRolesForUser(UserInterface $user)
    {
        $this->db->table('user_roles')
            ->where('user_id', '=', $user->getID())
            ->delete();
    }

    protected function insertRolesForUser(UserInterface $user)
    {
        if ($user->getRoles()) {
            foreach ($user->getRoles() as $role) {
                $this->db->table('user_roles')->insert([
                    'user_id' => $user->getID(),
                    'role_name' => $role->getMachineName()
                ]);
            }
        }
    }
}
