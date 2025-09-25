<?php

namespace App\Http\Requests\web\registrations\collaborators;

use Illuminate\Foundation\Http\FormRequest;

class CollaboratorStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'cpf' => preg_replace('/\D/', '', $this->cpf ?? ''),
            'zip_code' => preg_replace('/\D/', '', $this->zip_code ?? ''),
            'phone' => preg_replace('/\D/', '', $this->phone ?? ''),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:collaborators,email|max:255',
            'password' => 'required|string|min:6|max:255',
            'cpf' => 'required|string|size:11|unique:collaborators,cpf',
            'admission_date' => 'required|date',
            'phone' => 'required|string|min:10|max:11',
            'zip_code' => 'required|string|size:8',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:10',
            'neighborhood' => 'required|string|max:100',
            'position_id' => 'required|exists:positions,id',
            'work_hours_id' => 'required|exists:work_hours,id',
            'status' => 'required|in:ativo,inativo',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ter um formato válido.',
            'email.unique' => 'Este email já está em uso.',
            'email.max' => 'O email não pode ter mais de 255 caracteres.',
            'password.required' => 'A senha é obrigatória.',
            'password.string' => 'A senha deve ser um texto válido.',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
            'password.max' => 'A senha não pode ter mais de 255 caracteres.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.size' => 'O CPF deve ter 11 dígitos numéricos.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'admission_date.required' => 'A data de admissão é obrigatória.',
            'admission_date.date' => 'A data de admissão deve ser uma data válida.',
            'phone.required' => 'O telefone é obrigatório.',
            'phone.min' => 'O telefone deve ter pelo menos 10 dígitos.',
            'phone.max' => 'O telefone deve ter no máximo 11 dígitos.',
            'zip_code.required' => 'O CEP é obrigatório.',
            'zip_code.size' => 'O CEP deve ter 8 dígitos numéricos.',
            'street.required' => 'O endereço é obrigatório.',
            'street.max' => 'O endereço não pode ter mais de 255 caracteres.',
            'number.required' => 'O número é obrigatório.',
            'number.max' => 'O número não pode ter mais de 10 caracteres.',
            'neighborhood.required' => 'O bairro é obrigatório.',
            'neighborhood.max' => 'O bairro não pode ter mais de 100 caracteres.',
            'position_id.required' => 'O cargo é obrigatório.',
            'position_id.exists' => 'O cargo selecionado não existe.',
            'work_hours_id.required' => 'O horário de trabalho é obrigatório.',
            'work_hours_id.exists' => 'O horário de trabalho selecionado não existe.',
            'status.required' => 'O status é obrigatório.',
            'status.in' => 'O status deve ser "ativo" ou "inativo".',
        ];
    }
}
