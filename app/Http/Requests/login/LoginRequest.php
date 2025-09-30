<?php

namespace App\Http\Requests\login;

use App\Models\CollaboratorModel;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6'
        ];
    }

    /**
     * Validate that the email exists in either users or collaborators table
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $email = $this['email'];
            
            if ($email) {
                $userExists = User::where('email', $email)->exists();
                $collaboratorExists = CollaboratorModel::where('email', $email)->exists();
                
                if (!$userExists && !$collaboratorExists) {
                    $validator->errors()->add('email', 'Credenciais inválidas.');
                }
                
                // Verifica se o colaborador está ativo
                if ($collaboratorExists) {
                    $collaborator = CollaboratorModel::where('email', $email)->first();
                    if ($collaborator && $collaborator->status !== \App\Enums\CollaboratorStatusEnum::ACTIVE) {
                        $validator->errors()->add('email', 'Sua conta está inativa. Entre em contato com o administrador.');
                    }
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'Por favor, insira um email válido.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.string' => 'Credenciais inválidas.',
            'password.min' => 'Credenciais inválidas.',
        ];
    }

    /**
     * Determine if the email belongs to a collaborator
     */
    public function isCollaborator(): bool
    {
        $email = $this['email'];
        return $email ? CollaboratorModel::where('email', $email)->exists() : false;
    }

    /**
     * Determine if the email belongs to a user (manager)
     */
    public function isUser(): bool
    {
        $email = $this['email'];
        return $email ? User::where('email', $email)->exists() : false;
    }
}
