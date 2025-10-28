<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'first_name' => ['required', 'max:50'],
            'last_name' => ['required', 'max:50'],
            'email' => ['required', 'max:50', 'email', Rule::unique('users')->ignore($userId)],
            'password' => $this->isMethod('POST') ? ['required', 'min:8'] : ['nullable', 'min:8'],
            'role' => ['nullable', 'in:owner,user'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ];
    }
}

