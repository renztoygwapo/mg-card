<?php

namespace App\Http\Requests\PromoCustomerAvail;

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
            'date_expired' => 'required|date', 
            'promo_id' => 'required|integer|exists:promo_items,id', 
            'customer_id' => 'required|integer|exists:customers,id',
            'isAvail' => 'required|string',
            'serial_no' => 'string'
        ];
    }
}
