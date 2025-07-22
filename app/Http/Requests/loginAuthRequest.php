<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class loginAuthRequest extends FormRequest
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
            'email' => [
                'required',
                'email:rfc,dns', // Validação mais rigorosa de email
                'max:255',
                'exists:users,email' // Verifica se o email existe na tabela users
            ],
            'password' => [
                'required',
                'string',
                'min:6'
            ],
            'remember' => [
                'nullable',
                'boolean'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'Por favor, insira um email válido.',
            'email.exists' => 'Este email não está cadastrado em nosso sistema.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'email' => 'email',
            'password' => 'senha',
            'remember' => 'lembrar-me'
        ];
    }

    /**
     * Get the credentials for authentication.
     *
     * @return array<string, string>
     */
    public function getCredentials(): array
    {
        $validated = $this->validated();
        return [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ];
    }

    /**
     * Get the remember me boolean.
     */
    public function getRemember(): bool
    {
        $validated = $this->validated();
        return isset($validated['remember']) ? (bool) $validated['remember'] : false;
    }
}
