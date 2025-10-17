<?php

namespace App\Http\Requests\forEmployees\registrations;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegistrationsEmployeesUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('collaborator')->check();
    }

    public function rules(): array
    {
        return [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'A senha atual é obrigatória.',
            'new_password.required' => 'A nova senha é obrigatória.',
            'new_password.min' => 'A nova senha deve ter no mínimo 6 caracteres.',
            'new_password.confirmed' => 'As senhas não coincidem.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $collaborator = Auth::guard('collaborator')->user();

            if (! Hash::check($this['current_password'], $collaborator->password)) {
                $validator->errors()->add('current_password', 'A senha atual está incorreta.');
            }

            if (Hash::check($this['new_password'], $collaborator->password)) {
                $validator->errors()->add('new_password', 'A nova senha deve ser diferente da senha atual.');
            }
        });
    }
}
