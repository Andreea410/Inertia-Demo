<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganizationRequest extends FormRequest
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
        return [
            'name' => ['required', 'max:100'],
            'phone' => ['nullable', 'max:50'],
            'address' => ['nullable', 'max:150'],
            'city' => ['nullable', 'max:50'],
            'province' => ['nullable', 'max:50'],
            'country' => ['nullable', 'max:50'],
            'postal_code' => ['nullable', 'max:25'],
        ];
    }
}

