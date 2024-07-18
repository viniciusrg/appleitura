<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->permission->type === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'author' => 'required|string|max:255',
            'read_time' => 'required|string|max:255',
            'content_audio' => 'nullable|file|mimes:mp3,wav,m4a|max:10240',
            'cover' => 'nullable|file|image|max:5120',
            'categories_id' => 'nullable',
        ];
    }
}
