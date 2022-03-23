<?php

namespace App\Http\Requests\Price;

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
            'product_date' => 'required|date', 
            'product_id' => 'required|integer|exists:products,id', 
            'product_price' => 'required|numeric',
            'shift' => 'required|integer',
            'username' => 'required|string'
        ];
    }
}
