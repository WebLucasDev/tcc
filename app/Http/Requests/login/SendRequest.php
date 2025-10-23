<?php

namespace App\Http\Requests\login;

use App\Models\CollaboratorModel;
use App\Models\User;
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
            'email' => [
                'required',
                'email',
                'max:255',
                function ($attribute, $value, $fail) {
                    $existsInUsers = User::where('email', $value)->exists();
                    $existsInCollaborators = CollaboratorModel::where('email', $value)->exists();

                    if (!$existsInUsers && !$existsInCollaborators) {
                        $fail('E-mail não encontrado no sistema.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'Por favor, insira um email válido.',
            'email.max' => 'O email deve ter no máximo :max caracteres.',
        ];
    }
}
