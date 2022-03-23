<?php

namespace App\Http\Requests\Transaction;

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
            'customer_id' => 'required|integer|exists:customers,id',
            'product_id' => 'required|integer|exists:products,id',
            'customer_group_id' => 'required|integer|exists:customer_groups,id',
            'liters' => 'required|regex:/^\d*(\.\d{1,2})?$/',
            'price' => 'required|regex:/^\d*(\.\d{1,2})?$/',
            'amount' => 'required|regex:/^\d*(\.\d{1,2})?$/',
            'shift' => 'required|integer'
        ];
    }
}
