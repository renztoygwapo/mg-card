<?php

namespace App\Http\Requests\PointSystem;

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
            'customer_group_id' => 'required|exists:customer_groups,id',
            'product_id' => 'required|exists:products,id',
            'equivalent_points' => 'required|regex:/^\d*(\.\d{1,2})?$/'
        ];
    }
}
