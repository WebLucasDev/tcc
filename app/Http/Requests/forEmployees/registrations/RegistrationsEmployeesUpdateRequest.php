<?php

namespace App\Http\Requests\forEmployees\registrations;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegistrationsEmployeesUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('collaborator')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'current_password.required' => 'A senha atual é obrigatória.',
            'new_password.required' => 'A nova senha é obrigatória.',
            'new_password.min' => 'A nova senha deve ter no mínimo 6 caracteres.',
            'new_password.confirmed' => 'As senhas não coincidem.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $collaborator = Auth::guard('collaborator')->user();

            // Verifica se a senha atual está correta
            if (!Hash::check($this['current_password'], $collaborator->password)) {
                $validator->errors()->add('current_password', 'A senha atual está incorreta.');
            }

            // Verifica se a nova senha é diferente da atual
            if (Hash::check($this['new_password'], $collaborator->password)) {
                $validator->errors()->add('new_password', 'A nova senha deve ser diferente da senha atual.');
            }
        });
    }
}
