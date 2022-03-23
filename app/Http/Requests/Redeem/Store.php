<?php

namespace App\Http\Requests\Redeem;

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
            'customer_id' => 'required|integer|exists:customers,id',
            'serial_no' => 'required|max:30|unique:redeems',
            'amount' => 'required|regex:/^\d*(\.\d{1,2})?$/',
            'tax' => 'required|regex:/^\d*(\.\d{1,2})?$/',
            'shift' => 'required|integer',
            'username' => 'required|string',
            'redeem_type' => 'required|string'
        ];
    }
}
