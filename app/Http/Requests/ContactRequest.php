<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $contactId = $this->route('contact')?->id;

        return [
            'first_name' => ['required', 'max:50'],
            'last_name' => ['required', 'max:50'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'email' => ['nullable', 'max:50', 'email', Rule::unique('contacts')->ignore($contactId)],
            'phone' => ['nullable', 'max:50'],
            'address' => ['nullable', 'max:150'],
            'city' => ['nullable', 'max:50'],
            'province' => ['nullable', 'max:50'],
            'country' => ['nullable', 'max:50'],
            'postal_code' => ['nullable', 'max:25'],
        ];
    }
}

