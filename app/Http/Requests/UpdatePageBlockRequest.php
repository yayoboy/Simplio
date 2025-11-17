<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePageBlockRequest extends FormRequest
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
            'type' => ['sometimes', 'string', 'in:text,heading,image,gallery,video,html,button,divider,spacer,container,columns'],
            'name' => ['nullable', 'string', 'max:255'],
            'content' => ['sometimes', 'array'],
            'properties' => ['nullable', 'array'],
            'position' => ['nullable', 'array'],
            'order' => ['sometimes', 'integer', 'min:0'],
            'is_visible' => ['sometimes', 'boolean'],
            'parent_id' => ['nullable', 'string'],
        ];
    }
}
