<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteRequest extends FormRequest
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
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', 'unique:sites,slug,' . $this->route('site')->id],
            'description' => ['nullable', 'string'],
            'theme_id' => ['nullable', 'exists:themes,id'],
            'domain' => ['nullable', 'string', 'max:255'],
            'settings' => ['nullable', 'array'],
            'meta' => ['nullable', 'array'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }
}
