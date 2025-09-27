<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => ['nullable', 'integer', 'exists:categories,id'],
            'user'     => ['nullable', 'integer', 'exists:users,id'],
            'q'        => ['nullable', 'string', 'max:255'],
        ];
    }
}
