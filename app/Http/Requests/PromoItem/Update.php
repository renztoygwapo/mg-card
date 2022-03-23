<?php

namespace App\Http\Requests\PromoItem;

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
            'name' => 'required',
            'eq_points' => 'required',
            'date_start' => 'required',
            'date_end' => 'required'
        ];
    }
}
