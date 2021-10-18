<?php

namespace Fifthgate\Objectivity\Users\Http\Rules;

use Illuminate\Contracts\Validation\Rule;
use Fifthgate\Objectivity\Users\Service\Interfaces\UserServiceInterface;

class UserRolesExistRule implements Rule
{
    protected UserServiceInterface $userService;

    protected array $errors = [];
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $passes = true;

        if (!is_array($value) or empty($value)) {
            $passes = false;
            $this->errors[] = "Role input supplied to the '{$attribute}' field must be supplied as an array of role names.";
        }
        
        if ($passes) {
            foreach ($value as $candidateRoleName) {
                $role = $this->userService->getRoles()->getRoleByName($candidateRoleName);
                if (!$role) {
                    $passes = false;
                    $this->errors[] = "The role '{$candidateRoleName}' supplied for the '{$attribute}' field does not exist";
                }
            }
        }
        
        return $passes;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return implode(", ", $this->errors);
    }
}
