<?php

namespace App\Http\Requests\DatabaseManagement;

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
            'user' => 'required|max:255',
            'path' => 'required|max:255',
            'shift' => 'required|max:255'
        ];
    }
}
