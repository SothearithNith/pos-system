<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SizeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isPost = $this->isMethod('post');

        return [
            'name_en' => [
                $isPost ? 'required' : 'sometimes',
                'required',
                'string',
                Rule::unique('sizes', 'name_en')->ignore($this->route('size')),
            ],
            'name_kh' => [$isPost ? 'nullable' : 'sometimes', 'nullable', 'string'],
            'is_active' => [$isPost ? 'required' : 'sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name_en.required' => 'The English name is required.',
            'name_en.string' => 'The English name must be a string.',
            'name_en.unique' => 'This size name already exists.',

            'name_kh.string' => 'The Khmer name must be a string.',

            'is_active.required' => 'The status is required.',
            'is_active.boolean' => 'The status must be true or false.',
        ];
    }
}
