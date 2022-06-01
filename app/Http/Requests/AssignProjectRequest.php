<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\AlreadyHasOne;
use App\Rules\AlreadyAssigned;
use Illuminate\Http\Request;

class AssignProjectRequest extends FormRequest
{
    protected $redirect = 'projectAssignment';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'selected_project' => 'required',
            'selected_project_manager' => [new AlreadyHasOne(request()->input('selected_project'))],
            'selected_developers.*' => [new AlreadyAssigned(request()->input('selected_project'))],
            'selected_submitters.*' => [new AlreadyAssigned(request()->input('selected_project'))],            
        ];
    }
}
