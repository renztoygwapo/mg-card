<?php

namespace App\Http\Requests\GeneralTemplate;

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
            'date' => 'required|date|unique:generate_templates',
            'premium' => 'numeric|regex:/^\d*(\.\d{1,2})?$/',
            'unleaded' => 'numeric|regex:/^\d*(\.\d{1,2})?$/',
            'desiel' => 'numeric|regex:/^\d*(\.\d{1,2})?$/',
            'total' => 'numeric|regex:/^\d*(\.\d{1,2})?$/'
        ];
    }
}
