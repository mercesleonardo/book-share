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
            'status'   => ['nullable', 'in:pending,approved,rejected,flagged'],
            // 'author' agora representa o nome do autor do livro (campo textual), distinto de 'user' (quem cadastrou)
            'author'   => ['nullable', 'string', 'max:255'],
        ];
    }
}
