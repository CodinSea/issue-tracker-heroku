<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\ProjectPersonnel;
use App\Models\User;

class AlreadyHasOne implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
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
        $userIds = ProjectPersonnel::select('user_id')
                                        ->where('project_id', $this->id);
        $projectManager = User::whereIn('id', $userIds)
                                    ->where('role', 'Project manager')
                                    ->get();
        
        return $projectManager->isEmpty();       
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The project already has a project manager.';
    }
}
