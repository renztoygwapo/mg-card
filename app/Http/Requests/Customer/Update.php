<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
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
            'barcode' => 'required|max:30|unique:customers,barcode,' . $this->id,
            'reference_no' => 'max:30|unique:customers,reference_no,' . $this->id,
            'address' => 'required',
            'birthdate' => 'required',
            'mobile_no' => 'max:30',
            'expire_at' => 'required',
            'vehicles' => 'required',
            'customer_group_id' => 'required|exists:customer_groups,id',
            'shift' => 'required',
            'username' => 'required|string'
        ];
    }
}
