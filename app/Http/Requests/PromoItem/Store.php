<?php

namespace App\Http\Requests\PromoItem;

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
            'name' => 'required|max:255',
            'eq_points' => 'required|max:255',
            'date_start' => 'required',
            'date_end' => 'required',
            'point_types' => 'required'
        ];
    }
}
