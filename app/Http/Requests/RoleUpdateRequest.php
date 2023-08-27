<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->user('employee-api'))
        {
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $role_id=$this->route('role_permission');
        return [
            'role'=>['required','unique:roles,name,'.$role_id,'max:60'],
           'permissions' =>['required'],
            'permissions.*'=>['exists:permissions,name'],
        ];
    }
}
