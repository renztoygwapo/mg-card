<?php

namespace App\Http\Requests\GeneralTemplate;

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
            'date' => 'required|unique:generate_templates,date,' . $this->id, 
            'premium' => 'numeric|regex:/^\d*(\.\d{1,2})?$/', 
            'unleaded' => 'numeric|regex:/^\d*(\.\d{1,2})?$/',
            'desiel' => 'numeric|regex:/^\d*(\.\d{1,2})?$/'
        ];
    }
}
