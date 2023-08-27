<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class shippingRequest extends FormRequest
{
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
            'firstName' => 'required|string|between:2,100',
            'lastName' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:shippings',
            'password' => 'required|string|min:6',
          //  'accoutStatus' => 'required|boolean',
            'warehouseId' => 'required|integer',
        ];
    }
}
