<?php

namespace App\Http\Requests\login;

use Illuminate\Foundation\Http\FormRequest;

class SendRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|max:255|exists:users,email',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'Por favor, insira um email válido.',
            'email.exists' => 'Credenciais inválidas.',
        ];
    }
}
