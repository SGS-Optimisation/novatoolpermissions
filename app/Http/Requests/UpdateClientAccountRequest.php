<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientAccountRequest extends FormRequest
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
            'name' => 'required|unique:client_accounts,name,' . $this->get('slug').',slug',
            'slug' => 'required|max:30|unique:client_accounts,slug,' . $this->get('slug').',slug',
            //'image' => 'sometimes|image',
        ];
    }
}
