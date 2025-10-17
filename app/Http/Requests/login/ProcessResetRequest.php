<?php

namespace App\Http\Requests\login;

use Illuminate\Foundation\Http\FormRequest;

class ProcessResetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|confirmed|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => 'Token é obrigatório.',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'Por favor, insira um email válido.',
            'email.exists' => 'Email não encontrado.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
        ];
    }
}
