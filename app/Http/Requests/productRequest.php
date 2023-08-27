<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class productRequest extends FormRequest
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
            'name' => 'required|string',
            'code' => 'required|string',
            'image' => 'required|string|between:2,100',
            'weight' => 'required|numeric',
            'price'=>'required|numeric',
            'available'=>'required|boolean',
            'description'=>'required|string',
            'categorytId'=>'required|integer',

        ];
    }
}
