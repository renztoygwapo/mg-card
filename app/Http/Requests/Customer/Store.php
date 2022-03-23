<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
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
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'barcode' => 'required|max:30|unique:customers',
            'reference_no' => 'required|max:30|unique:customers',
            'address' => 'required',
            'birthdate' => 'required',
            'mobile_no' => 'required',
            'expire_at' => 'required',
            'customer_group_id' => 'required|exists:customer_groups,id',
            'vehicles' => 'required',
            'shift' => 'required',
            'username' => 'required|string',
            'is_admin' => 'max:3'
        ];
    }
}
