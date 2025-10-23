<?php

namespace App\Http\Requests\login;

use App\Models\CollaboratorModel;
use App\Models\User;
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
            'token' => 'required|string',
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) {
                    $existsInUsers = User::where('email', $value)->exists();
                    $existsInCollaborators = CollaboratorModel::where('email', $value)->exists();

                    if (!$existsInUsers && !$existsInCollaborators) {
                        $fail('E-mail não encontrado no sistema.');
                    }
                },
            ],
            'password' => 'required|string|confirmed|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => 'Token de recuperação é obrigatório.',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'Por favor, insira um email válido.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'password.min' => 'A senha deve ter no mínimo :min caracteres.',
        ];
    }
}
