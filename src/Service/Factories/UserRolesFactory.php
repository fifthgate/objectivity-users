<?php

namespace Fifthgate\Objectivity\Users\Service\Factories;

use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use \Exception;
use Fifthgate\Objectivity\Users\Domain\ValueObjects\UserRole;
use Fifthgate\Objectivity\Users\Domain\Collection\UserRoleCollection;
use Fifthgate\Objectivity\Users\Service\UserService;
use Fifthgate\Objectivity\Users\Infrastructure\Repository\Interfaces\UserRepositoryInterface;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class UserRolesFactory
{
    const CACHEKEY = 'user_service_config_cache';

    public function __invoke(bool $testMode = false)
    {
        $rolesCollection = Cache::get(self::CACHEKEY);
        /**
         * Rebuild the index if there isn't a cached version available.
         */
        if (!$rolesCollection or $testMode) {
            $date = new Carbon;
            Log::info("Role cache rebuilt at {$date}");
            $roleConfig = File::get(base_path().'/services/User/Service/Config/roles.json');
            $rolesArray = json_decode($roleConfig, true);
            foreach ($rolesArray as $roleMachineName => $roleDefinitionArray) {
                $this->validateRole($roleMachineName, $roleDefinitionArray);
            }
            $rolesCollection = new UserRoleCollection;
            foreach ($rolesArray as $roleMachineName => $roleDefinitionArray) {
                $rolesCollection->add(
                    new UserRole(
                        $roleMachineName,
                        $roleDefinitionArray['name'],
                        $roleDefinitionArray['description'],
                        $roleDefinitionArray['permissions']
                    )
                );
            }
            Cache::set(self::CACHEKEY, $rolesCollection);
        }
        return $rolesCollection;
    }
    
    /**
    * @codeCoverageIgnore
    */
    private function validateRole(string $roleMachineName, array $roleDefinitionArray)
    {
        $requiredKeys = [
            'name' => 'string',
            'description' => 'string',
            'permissions' => 'array'
        ];
        
        if (!is_string($roleMachineName) or preg_match('/[^a-z_\-0-9]/i', $roleMachineName)) {
            throw new Exception("The Role's machine name must be an alphanumeric string with no spaces");
        }
        foreach ($requiredKeys as $key => $type) {
            if (!isset($roleDefinitionArray[$key])) {
                throw new Exception("`{$key}` must be present in the configuration");
            }
            switch ($type) {
                case "string":
                    if (!is_string($roleDefinitionArray[$key])) {
                        throw new Exception("`{$key}` must be a string");
                    }
                    break;
                case "array":
                    if (!is_array($roleDefinitionArray[$key])) {
                        throw new Exception("`{$key}` must be an array");
                    } else {
                        if (empty($roleDefinitionArray[$key])) {
                            throw new Exception("The key `{$key}` may not be empty.");
                        }
                    }
                    break;
            }
        }
    }
}
