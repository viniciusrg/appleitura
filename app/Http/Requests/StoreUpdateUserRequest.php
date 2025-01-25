<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'email' => [
                'required',
                'email',
                'max:255',
                "unique:users"
            ],
            'password' => [
                'required',
                'min:6',
                'max:100',
            ],
        ];

        if ($this->method() === 'PATCH') {
            $rules['email'] = [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->id),
            ];

            $rules['password'] = [
                'nullable',
                'min:6',
                'max:100',
            ];
        }

        return $rules;
    }
}
