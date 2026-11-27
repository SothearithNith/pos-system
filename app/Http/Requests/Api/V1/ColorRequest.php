<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ColorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isStore = $this->isMethod('post');

        return [
            'name_en' => [
                $isStore ? 'required' : 'sometimes',
                'required',
                'string',
                Rule::unique('colors', 'name_en')->ignore($this->route('color')),
            ],
            'name_kh' => [$isStore ? 'nullable' : 'sometimes', 'nullable', 'string'],
            'code' => [$isStore ? 'nullable' : 'sometimes', 'nullable', 'string'],
            'is_active' => [$isStore ? 'required' : 'sometimes', 'boolean']
        ];
    }

    public function messages(): array
    {
        return [
            'name_en.required' => 'The English color name is required.',
            'name_en.string' => 'The English color name must be a string.',
            'name_en.unique' => 'This English color name already exists.',
            'name_kh.string' => 'The Khmer color name must be a string.',
            'code.string' => 'The color code must be a string.',
            'is_active.required' => 'The color status is required.',
            'is_active.boolean' => 'The color status must be true or false.',
        ];
    }
}
