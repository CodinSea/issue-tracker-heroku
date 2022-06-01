<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\ProjectPersonnel;
use App\Models\User;

class AlreadyAssigned implements Rule
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
        $project_personnel = ProjectPersonnel::where('project_id', $this->id)
                                                ->pluck('user_id')
                                                ->toArray();

        return (!in_array($value, $project_personnel));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You are trying to assign someone who has already been assigned to this project.';
    }
}
