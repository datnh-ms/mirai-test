<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if($this->isMethod('post')){
            // Rules for create account
            return [
                'login'    => 'required|string|max:20|unique:accounts,login',
                'password' => 'required|string|max:40',
                'phone'    => 'nullable|string|max:20'
            ];
        } else {
            // Rules for updating an existing post
            return [
                'login'    => [
                    "required", 
                    "string",
                    "max:20",
                    Rule::unique('accounts')->ignore($this->id, 'registerID')
                ],
                'password' => 'required|string|max:40',
                'phone'    => 'nullable|string|max:20'
            ];
        }
    }
}
